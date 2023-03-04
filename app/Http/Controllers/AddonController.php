<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\AddonsChangesLog;
use App\Menu;
use Illuminate\Http\Request;
use ZipArchive;
use DB;
use Auth;
use App\Models\BusinessSetting;
use CoreComponentRepository;
use Illuminate\Support\Str;
use Storage;
use Cache;
use File;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.addons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.addons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Cache::forget('addons');

        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return back();
        }

        if (class_exists('ZipArchive')) {
            if ($request->hasFile('addon_zip')) {
                // Create update directory.
                $dir = 'addons';
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $path = Storage::disk('local')->put('addons', $request->addon_zip);

                $zipped_file_name = $request->addon_zip->getClientOriginalName();

                //Unzip uploaded update file and remove zip file.
                $zip = new ZipArchive;
                $res = $zip->open(base_path('public/' . $path));

                $random_dir = Str::random(10);

                $dir = trim($zip->getNameIndex(0), '/');

                if ($res === true) {
                    $res = $zip->extractTo(base_path('temp/' . $random_dir . '/addons'));
                    $zip->close();
                } else {
                    dd('could not open');
                }

                $str = file_get_contents(base_path('temp/' . $random_dir . '/addons/' . $dir . '/config.json'));
                $json = json_decode($str, true);

                //dd($random_dir, $json);

                if (BusinessSetting::where('type', 'current_version')->first()->value >= $json['minimum_item_version']) {
                    if (count(Addon::where('unique_identifier', $json['unique_identifier'])->get()) == 0) {
                        $addon = new Addon;
                        $addon->name = $json['name'];
                        $addon->unique_identifier = $json['unique_identifier'];
                        $addon->version = $json['version'];
                        $addon->activated = 1;
                        $addon->image = $json['addon_banner'];
                        $addon->purchase_code = $json['purchase_code'] ?? null;
                        $addon->description = $json['description'] ?? null;
                        $addon->save();

                        // Create new directories.
                        if (!empty($json['directory'])) {
                            //dd($json['directory'][0]['name']);
                            foreach ($json['directory'][0]['name'] as $directory) {
                                if (is_dir(base_path($directory)) == false) {
                                    mkdir(base_path($directory), 0777, true);

                                } else {
                                    echo "error on creating directory";
                                }

                            }
                        }

                        // Create/Replace new files.
                        if (!empty($json['files'])) {
                            $create_log_addon = $this->createLogAddons($addon->id, 'public/'.$path, 'new', null); //log add zip file
                            foreach ($json['files'] as $file) {
                                if(file_exists(base_path($file['update_directory']))) {
                                    $updated_file_addon = $this->updatedFileAddons($file['update_directory'], $random_dir);
                                    copy(base_path($file['update_directory']), base_path($updated_file_addon));
                                    $create_log_addon = $this->createLogAddons($addon->id, $file['update_directory'], 'modified', $updated_file_addon);
                                } else {
                                    $create_log_addon = $this->createLogAddons($addon->id, $file['update_directory'], 'new', null);
                                }
                                
                                copy(base_path('temp/' . $random_dir . '/' . $file['root_directory']), base_path($file['update_directory']));
                            }

                        }

                        // Run sql modifications
                        $sql_path = base_path('temp/' . $random_dir . '/addons/' . $dir . '/sql/update.sql');
                        if (file_exists($sql_path)) {
                            DB::unprepared(file_get_contents($sql_path));
                        }

                        flash(translate('Addon installed successfully'))->success();
                        return redirect()->route('addons.index');
                    } else {
                        // Create new directories.
                        if (!empty($json['directory'])) {
                            //dd($json['directory'][0]['name']);
                            foreach ($json['directory'][0]['name'] as $directory) {
                                if (is_dir(base_path($directory)) == false) {
                                    mkdir(base_path($directory), 0777, true);

                                } else {
                                    echo "error on creating directory";
                                }

                            }
                        }

                        $addon = Addon::where('unique_identifier', $json['unique_identifier'])->first();

                        // Create/Replace new files.
                        if (!empty($json['files'])) {
                            foreach ($json['files'] as $file) {
                                if(file_exists(base_path($file['update_directory']))) {
                                    $check_existing_log = AddonsChangesLog::where('addon_id', $addon->id)->where('file_changes_directory', $file['update_directory'])->first();

                                    if(!empty($check_existing_log)) {
                                        $updated_file_addon = $this->updatedFileAddons($file['update_directory'], $random_dir);
                                        copy(base_path($file['update_directory']), base_path($updated_file_addon));
                                        $create_log_addon = $this->createLogAddons($addon->id, $file['update_directory'], 'updated', $updated_file_addon);
                                    } else {
                                        $updated_file_addon = $this->updatedFileAddons($file['update_directory'], $random_dir);
                                        copy(base_path($file['update_directory']), base_path($updated_file_addon));
                                        $create_log_addon = $this->createLogAddons($addon->id, $file['update_directory'], 'modified', $updated_file_addon);
                                    }
                                    
                                } else {
                                    $create_log_addon = $this->createLogAddons($addon->id, $file['update_directory'], 'new', null);
                                }

                                copy(base_path('temp/' . $random_dir . '/' . $file['root_directory']), base_path($file['update_directory']));
                            }

                        }

                        for ($i = $addon->version + 0.05; $i <= $json['version']; $i = $i + 0.1) {
                            // Run sql modifications
                            $sql_version = $i+0.05;
                            $sql_path = base_path('temp/' . $random_dir . '/addons/' . $dir . '/sql/' . $sql_version . '.sql');
                            if (file_exists($sql_path)) {
                                DB::unprepared(file_get_contents($sql_path));
                            }
                        }

                        $addon->version = $json['version'];
                        $addon->purchase_code = $json['purchase_code'] ?? null;
                        $addon->description = $json['description'] ?? null;
                        $addon->save();

                        flash(translate('This addon is updated successfully'))->success();
                        return redirect()->route('addons.index');
                    }
                } else {
                    flash(translate('This version is not capable of installing Addons, Please update.'))->error();
                    return redirect()->route('addons.index');
                }
            }
        }
        else {
            flash(translate('Please enable ZipArchive extension.'))->error();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function show(Addon $addon)
    {
        //
    }

    public function list()
    {
        //return view('backend.'.Auth::user()->role.'.addon.list')->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function edit(Addon $addon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Addon $addon
     * @return \Illuminate\Http\Response
     */
    public function activation(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return 0;
        }
        $addon = Addon::find($request->id);
        $addon->activated = $request->status;
        $addon->save();

        Cache::forget('addons');

        return 1;
    }

    public function delete_addon($id)
    {
        $addon = Addon::find($id);
        if ($addon) {

            if (strpos($addon->unique_identifier, 'laravolt') == 'true') {
                flash(translate('Sorry, you cannot delete this plugin'))->error();
                return redirect()->route('addons.index');
            }

            $addon_log = AddonsChangesLog::where('addon_id', $id)->get();
            if (sizeof($addon_log) > 0) {

                $error_message = array();
                foreach ($addon_log as $log){ // handling delete plugin if there is any changes in other addon
                    if ($log->status != 'updated') {
                        $check_file_another_addons = AddonsChangesLog::where('file_changes_directory', $log->file_changes_directory)->where('addon_id', '!=', $id)->get();
                        if (count($check_file_another_addons) > 0) {
                            $log_last_changes = AddonsChangesLog::where('file_changes_directory', $log->file_changes_directory)->where('addon_id', '!=', $id)->latest()->first();
                            if ($log->created_at > $log_last_changes->created_at) {
                                continue;
                            } else {
                                $addon_data = Addon::find($log_last_changes->addon_id);
                                $detail_message = 'File ' .$log->file_changes_directory. ' depends with plugin: ' .$addon_data->name .', ';
                                array_push($error_message, $detail_message);
                            }
                        } else {
                            continue;
                        }
                    }
                }
                
                if(sizeof($error_message) > 0) {
                    $message = 'ERROR: Cannot delete! ';
                    foreach ($error_message as $msg) {
                        $message .= $msg;
                    }
                    $message .= ' Please check before deleting.';
                    flash(translate($message))->error();
                    return redirect()->route('addons.index');
                }

                // return "PASSED! You can delete this plugin"; // if all file is not used or not depend with another addon
                foreach ($addon_log as $log_delete) {
                    if ($log_delete->status == 'modified') {
                        copy(base_path($log_delete->file_before_changes), base_path($log_delete->file_changes_directory));
                    } else if ($log_delete->status == 'new') {
                        File::delete($log->file_changes_directory);
                    }
                    $log_delete->delete();
                }      

                $addon->delete();

                flash(translate('Addon deleted successfully'))->success();
                return redirect()->route('addons.index');
                
            } else {
                flash(translate('Cannot delete plugin. The changes file is untracked with the system'))->error();
                return redirect()->route('addons.index');
            }

        } else {
            flash(translate('Addon not found'))->error();
            return redirect()->route('addons.index');
        }

    }

    private function createLogAddons($addon_id, $file, $status, $old_file = null)
    {
        $addonLog = new AddonsChangesLog;
        $addonLog->addon_id = $addon_id;
        $addonLog->file_before_changes = $old_file;    
        $addonLog->file_changes_directory = $file;
        $addonLog->status = $status;
        $addonLog->save();
    }

    private function updatedFileAddons($file_update_directory, $random_dir)
    {
        $value = explode('/', $file_update_directory);
        $filePath = 'temp/' . $random_dir . '/addons/';
        $tempName = end($value);
        $fileName = explode('.', $tempName);
        if (count($fileName) == 3) {
            $oldFileName = $fileName[0] .'_'. now()->timestamp . '.' . $fileName[1] . '.' . $fileName[2];
        } else {
            $oldFileName = $fileName[0] .'_'. now()->timestamp . '.' . $fileName[1];
        }
        $moveFilePathName = $filePath . $oldFileName;

        return $moveFilePathName;
    }

    public function detail_log_addons($id)
    {
        $addon_logs = AddonsChangesLog::where('addon_id', $id)->get();
        $addon = Addon::find($id);
        return view('backend.addons.log', compact('addon_logs', 'addon'));
    }
}
