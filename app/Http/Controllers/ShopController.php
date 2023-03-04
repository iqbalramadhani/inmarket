<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\Seller;
use App\Models\Courier;
use App\Models\RajaOngkirCity;
use App\Models\RajaOngkirSubdistrict;
use App\Models\RajaOngkirProvince;
use App\Models\BusinessSetting;
use App\Models\Verification;
use App\Models\IndonesiaProvince;
use App\Models\IndonesiaCity;
use App\Models\IndonesiaDistrict;
use App\Models\IndonesiaVillage;
use App\Models\Upload;
use Auth;
use Hash;
use Validator;
use App\Notifications\EmailVerificationNotification;
use Image;
use Storage;

class ShopController extends Controller
{

    public function __construct()
    {
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        $seller = json_decode(Seller::where('user_id', Auth()->user()->id)->get()->first()->verification_info);

        $city = RajaOngkirCity::where('city_id', $shop->city_id)->first();
        $sub_district = RajaOngkirSubdistrict::where('subdistrict_id', $shop->subdistrict_id)->first();

        if(!$city){
            $province_id = NULL;
        }else{
            $province_id = $shop->province_id;
        }
        if(!$sub_district){
            $sub_district_id = NULL;
        }else{
            $sub_district_id = $sub_district->city_id;
        }

        $locations = [
            'provinces' => RajaOngkirProvince::orderBy('province_name', 'ASC')->get(),
            'cities' => RajaOngkirCity::where('province_id', $province_id)->orderBy('city_name', 'ASC')->get(),
            'sub_districts' => RajaOngkirSubdistrict::where('city_id', $sub_district_id)->orderBy('subdistrict_name', 'ASC')->get(),
        ];

        $laravolt_locations = $this->get_data_laravolt($shop);
        $couriers = Courier::all();

        return view('frontend.user.seller.shop', compact('shop', 'seller', 'locations', 'couriers', 'laravolt_locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::check() && Auth::user()->user_type == 'admin'){
            flash(translate('Admin can not be a seller'))->error();
            return back();
        }
        else{
            return view('frontend.seller_form');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'input-logo' => 'required|max:1024'
        ];
    
        $customMessages = [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'email' => ':attribute harus berupa email yang valid.',
            'unique' => ':attribute sudah terdaftar.',
            'boolean' => ':attribute harus berupa boolean.',
            'confirmed' => 'Konfirmasi password harus sama dengan password.'
        ];

        $this->validate($request, $rules, $customMessages);


        $user = null;
        if(!Auth::check()){

            $rules = [
                'password' => 'required|string|min:6|confirmed',
                'email' => 'required|email|unique:users,email',
                'is_agree_tos_seller' => 'required|boolean',
                'input-logo' => 'required|image|mimes:jpg,png,jpeg|max:1024'
            ];
            
            $this->validate($request, $rules, $customMessages);
            
            // if(User::where('email', $request->email)->first() != null){
            //     flash(translate('Email already exists!'))->error();
            //     return back();
            // }
            if($request->password == $request->password_confirmation){
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = "seller";
                $user->password = Hash::make($request->password);
                $user->is_agree_tos_seller = true;
                $user->save();
            }
            // else{
            //     flash(translate('Sorry! Password did not match.'))->error();
            //     return back();
            // }
        }else{

            $user = Auth::user();
            if($user->customer != null){
                $user->customer->delete();
            }
            $user->is_agree_tos_seller = true;
            $user->user_type = "seller";
            $user->save();
        }

        $seller = new Seller;
        $seller->user_id = $user->id;
        $seller->save();

        $upload = new Upload();
        $extension = strtolower($request->file('input-logo')->getClientOriginalExtension());

        $upload->file_original_name = null;
        $arr = explode('.', $request->file('input-logo')->getClientOriginalName());
        for($i=0; $i < count($arr)-1; $i++){
            if($i == 0){
                $upload->file_original_name .= $arr[$i];
            }
            else{
                $upload->file_original_name .= ".".$arr[$i];
            }
        }

        $path = $request->file('input-logo')->store('uploads/all', 'local');
        $size = $request->file('input-logo')->getSize();

        // Return MIME type ala mimetype extension
        $finfo = finfo_open(FILEINFO_MIME_TYPE); 

        // Get the MIME type of the file
        $file_mime = finfo_file($finfo, base_path('public/').$path);
        
        if (env('FILESYSTEM_DRIVER') == 's3') {
            Storage::disk('s3')->put(
                $path,
                file_get_contents(base_path('public/').$path),
                [
                    'visibility' => 'public',
                    'ContentType' =>  $extension == 'svg' ? 'image/svg+xml' : $file_mime
                ]
            );
            if($arr[0] != 'updates') {
                unlink(base_path('public/').$path);
            }
        }

        $upload->extension = $extension;
        $upload->file_name = $path;
        $upload->user_id = $user->id;
        $upload->type = "image";
        $upload->file_size = $size;
        $upload->save();

        if(Shop::where('user_id', $user->id)->first() == null){
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->address;

            $shop->indonesia_province_id = $request->province;
            $shop->indonesia_city_id = $request->city;
            $shop->indonesia_district_id = $request->district;
            $shop->indonesia_subdistrict_id = $request->sub_district;
            $shop->postal_code = $request->postal_code;
            $shop->logo = $upload->id;

            $shop->slug = preg_replace('/\s+/', '-', $request->name).'-'.$shop->id;

            if($shop->save()){
                auth()->login($user, false);
                if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                }
                else {
                    $user->notify(new EmailVerificationNotification());
                }

                $data = array();
                $i = 0;
                foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
                    $item = array();
                    if ($element->type == 'text') {
                        $item['type'] = 'text';
                        $item['label'] = $element->label;
                        $item['value'] = $request['element_' . $i];
                    } elseif ($element->type == 'select' || $element->type == 'radio') {
                        $item['type'] = 'select';
                        $item['label'] = $element->label;
                        $item['value'] = $request['element_' . $i];
                    } elseif ($element->type == 'multi_select') {
                        $item['type'] = 'multi_select';
                        $item['label'] = $element->label;
                        $item['value'] = $request['element_' . $i] ? json_encode($request['element_' . $i]) : null;
                    } elseif ($element->type == 'file') {
                        $item['type'] = 'file';
                        $item['label'] = $element->label;
                        $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
                    } elseif ($element->type == 'location') {
                        $item['type'] = 'location';
                        $item['label'] = $element->label;
                        $item['value'] = null;
                    }
                    array_push($data, $item);
                    $i++;
                }
                $seller->verification_info = json_encode($data);

                if ($seller->save()) {

                    flash(translate('Your Shop has been created successfully!, and verification request has been submitted successfully'))->success();
                    return redirect()->route('dashboard');
                }
            }
            else{
                $seller->delete();
                $user->user_type == 'customer';
                $user->save();
            }
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::find($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'regex:^[0-9\-\+]{9,15}$^'
            ],
        ]);

        if(isset($request->_validate) && $request->_validate == 0) {
            //null
        } else {    
            if ($validate->fails()) {
                return redirect()->route('shops.index')
                ->withErrors($validate);
            }
        }

        try {
            if($request->has('name') && $request->has('address_laravolt')){
                if ($request->has('shipping_cost')) {
                    $shop->shipping_cost = $request->shipping_cost;
                }
                
                $shop->name             = $request->name;
                $shop->address          = $request->address_laravolt;
                $shop->phone            = $request->phone;
                $shop->slug             = preg_replace('/\s+/', '-', $request->name).'-'.$shop->id;
                $shop->meta_title       = $request->meta_title ?? null;
                $shop->meta_description = $request->meta_description ?? null;
                $shop->logo             = $request->logo ?? null;
    
                $shop->indonesia_province_id = $request->province_laravolt;
                $shop->indonesia_city_id = $request->city_laravolt;
                $shop->indonesia_district_id = $request->district_laravolt;
                $shop->indonesia_subdistrict_id = $request->sub_district_laravolt;
                $shop->postal_code = $request->postal_code;
    
                if ($request->has('pick_up_point_id')) {
                    $shop->pick_up_point_id = json_encode($request->pick_up_point_id);
                } else {
                    $shop->pick_up_point_id = json_encode(array());
                }
            } else if ($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')){
                $shop->facebook = $request->facebook;
                $shop->google = $request->google;
                $shop->twitter = $request->twitter;
                $shop->youtube = $request->youtube;
            } else {
                $shop->sliders = $request->sliders;
            }
    
            if($shop->save()){
                flash(translate('Your Shop has been updated successfully!'))->success();
                return back();
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function verify_form(Request $request)
    {
        if(Auth::user()->seller->verification_info == null){
            $shop = Auth::user()->shop;
            return view('frontend.user.seller.verify_form', compact('shop'));
        }
        else {
            flash(translate('Sorry! You have sent verification request already.'))->error();
            return back();
        }
    }

    public function verify_form_store(Request $request)
    {
        $data = array();
        $i = 0;
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();
            if ($element->type == 'text') {
                $item['type'] = 'text';
                $item['label'] = $element->label;
                $item['value'] = $request['element_'.$i];
            }
            elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_'.$i];
            }
            elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_'.$i]);
            }
            elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_'.$i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $seller = Auth::user()->seller;
        $seller->verification_info = json_encode($data);
        if($seller->save()){
            flash(translate('Your shop verification request has been submitted successfully!'))->success();
            return redirect()->route('dashboard');
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function verify_form_update(Request $request)
    {
        $oldData = json_decode(Seller::where('user_id', Auth()->user()->id)->get()->first()->verification_info) ?? [];
        $data = array();
        $dataChange = array();
        $i = 0;
        try {
            foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
                $item = array();
    
                $keys = array_keys(array_column($oldData, 'type'), $element->type);
                foreach ($keys as $kunci) {
                    $item = [];
    
                    if ($oldData[$kunci]->label == $element->label && ($request['element_' . $i] == $oldData[$kunci]->value || $request['element_' . $i] == null)) {
                        $item['label'] = $element->label;
                        $item['value'] = $oldData[$kunci]->value;
                        $item['type'] = $element->type;
                        array_push($data, $item);
                        $i++;
                        continue 2;
                    } else if ($oldData[$kunci]->label == $element->label && $request['element_' . $i] != $oldData[$kunci]->value) {
                        $item['label'] = $element->label;
                        $item['value'] = $oldData[$kunci]->value;
                        $item['type'] = $element->type;
                        $item['old_value'] = $oldData[$kunci]->value;
                        break;
                    }
                }
        
                if ($element->type == 'text') {
                    $item['type'] = $element->type;
                    $item['label'] = $element->label;
                    $item['value'] = $request['element_' . $i];
                } elseif ($element->type == 'select' || $element->type == 'radio') {
                    $item['type'] = $element->type;
                    $item['label'] = $element->label;
                    $item['value'] = $request['element_' . $i];
                } elseif ($element->type == 'multi_select') {
                    $item['type'] = $element->type;
                    $item['label'] = $element->label;
                    $item['value'] = json_encode($request['element_' . $i]);
                } elseif ($element->type == 'file') {
                    $item['type'] = $element->type;
                    $item['label'] = $element->label;
                    $item['value'] = '';
                    if($request['element_' . $i]) {
                        $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
                    }
                } elseif ($element->type == 'location') {
                    $item['type'] = 'location';
                    $item['label'] = $element->label;
                    $item['value'] = null;
                }
    
                array_push($dataChange, $item);
                unset($item['old_value']);
                array_push($data, $item);
                $i++;
            }
    
            // dd($data, $dataChange);
            $seller = Auth::user()->seller;
            $seller->verification_info = json_encode($data);
    
            if(count($dataChange)) {
                $verification = Verification::create([
                    'user_id' => Auth()->user()->id,
                    'details' => json_encode($dataChange),
                ]);
            }
    
            if ($seller->save()) {
                flash(translate('Your shop verification request has been updated successfully!'))->success();
                return back();
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }
        
    }

    private function get_data_laravolt($shop)
    {
        $city = IndonesiaCity::where('code', $shop->indonesia_city_id)->first();
        $district = IndonesiaDistrict::where('code', $shop->indonesia_district_id)->first();
        $village = IndonesiaVillage::where('code', $shop->indonesia_village_id)->first();

        if(!$city){
            $indonesia_province_id = NULL;
        }else{
            $indonesia_province_id = $shop->indonesia_province_id;
        }

        if (!$district) {
            $indonesia_city_id = NULL;
        }else{
            $indonesia_city_id = $shop->indonesia_city_id;
        }

        if (!$village) {
            $indonesia_district_id = NULL;
        }else{
            $indonesia_district_id = $shop->indonesia_district_id;
        }
        
        return $datas = [
            'provinces' => IndonesiaProvince::orderBy('name', 'ASC')->get(),
            'cities' => IndonesiaCity::where('province_code', $shop->indonesia_province_id)->orderBy('name', 'ASC')->get(),
            'districts' => IndonesiaDistrict::where('city_code', $shop->indonesia_city_id)->orderBy('name', 'ASC')->get(),
            'villages' => IndonesiaVillage::where('district_code', $shop->indonesia_district_id)->orderBy('name', 'ASC')->get()
        ];
    }
}
