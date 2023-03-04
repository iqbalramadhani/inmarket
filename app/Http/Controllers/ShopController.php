<?php

namespace App\Http\Controllers;

use App\BusinessSetting;
use App\Models\Courier;
use App\Models\RajaOngkirCity;
use App\Models\RajaOngkirSubdistrict;
use App\Models\RajaOnkirProvince;
use App\Notifications\EmailVerificationNotification;
use App\Seller;
use App\Shop;
use App\Models\Verification;
use App\User;
use App\Mail\ShopStatusChange;
use Illuminate\Support\Facades\Mail;
use Auth;
use Hash;
use Illuminate\Http\Request;

use App\Models\City;
use App\Models\District;
use App\Models\PostalCode;
use App\Models\Province;
use App\Models\SubDistrict;
use Validator;
use App\Mail\UserRegistered;
Use App\Mail\SellerVerification;
Use App\Mail\SellerVerificationAdmin;

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
            'provinces' => RajaOnkirProvince::orderby('province_name', 'ASC')->get(),
            'cities' => RajaOngkirCity::where('province_id', $province_id)->orderby('city_name', 'ASC')->get(),
            'sub_districts' => RajaOngkirSubdistrict::where('city_id', $sub_district_id)->orderby('subdistrict_name', 'ASC')->get(),
        ];

        $couriers = Courier::all();

        return view('frontend.user.seller.shop', compact('shop', 'seller', 'locations', 'couriers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check() && Auth::user()->user_type == 'admin') {
            flash(translate('Admin can not be a seller'))->error();
            return back();
        } else {
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
        $this->validate($request, [
            'is_agree_tos_seller' => 'required|boolean'
        ]);

        $user = null;
        if (!Auth::check()) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email already exists!'))->error();
                return back();
            }
            if ($request->password == $request->password_confirmation) {
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = "seller";
                $user->password = Hash::make($request->password);
                $user->is_agree_tos_seller = true;
                $save = $user->save();

                if($save) {
                    $template = new UserRegistered($user);
                        Mail::to($user->email)->send($template);
                }


            } else {
                flash(translate('Sorry! Password did not match.'))->error();
                return back();
            }
        } else {
            $user = Auth::user();
            if ($user->customer != null) {
                $user->customer->delete();
            }
            $user->is_agree_tos_seller = true;
            $user->user_type = "seller";
            $user->save();
        }

        $seller = new Seller;
        $seller->user_id = $user->id;
        $seller->save();

        if (Shop::where('user_id', $user->id)->first() == null) {
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->name;
            $shop->address = $request->address;

            $shop->city = City::find($request->input('city'))->name;
            $shop->province = Province::find($request->input('province'))->name;
            $shop->district = District::find($request->input('district'))->name;
            $shop->sub_district = SubDistrict::find($request->input('sub_district'))->name;
            $shop->postal_code = PostalCode::find($request->input('postal_code'))->kodepos;

            $shop->slug = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;

            if ($shop->save()) {

                $staff = User::where('user_type', 'staff')->get();
                // dd($admin);

                $template_admin = new SellerVerificationAdmin($shop);
                foreach($staff as $re) {
                    Mail::to($re->email)->send($template_admin);
                }

                $template = new SellerVerification($shop);
                Mail::to($user->email)->send($template);

                auth()->login($user, false);
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                } else {
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
                        $item['value'] = json_encode($request['element_' . $i]);
                    } elseif ($element->type == 'file') {
                        $item['type'] = 'file';
                        $item['label'] = $element->label;
                        $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
                    }
                    array_push($data, $item);
                    $i++;
                }

                $seller->verification_info = json_encode($data);
                if ($seller->save()) {
                    // flash(translate('Your shop verification request has been submitted successfully!'))->success();
                    // return redirect()->route('dashboard');
                    flash(translate('Your Shop has been created successfully!, and verification request has been submitted successfully'))->success();
                    return redirect()->route('dashboard');
                }
            } else {
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

        // dd($request->all());
        $c = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'regex:^[0-9\-\+]{9,15}$^'
            ],
        ]);

        if(isset($request->_validate) && $request->_validate == 0) {
            //null
        } else {    
            if ($c->fails()) {
                return redirect()->route('shops.index')
                ->withErrors($c);
            }
        }

        if ($request->has('name') && $request->has('address')) {
            $shop->name = $request->name;
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }
            $shop->address = $request->address;

            $shop->city_id = $request->input('city');
            $shop->province_id = $request->input('province');
            $shop->subdistrict_id = $request->input('sub_district');
            $shop->postal_code = $request->input('postal_code');

            $shop->phone = $request->phone;
            $shop->slug = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;

            $shop->meta_title = $request->meta_title;
            $shop->meta_description = $request->meta_description;
            $shop->logo = $request->logo;

            if ($request->has('pick_up_point_id')) {
                $shop->pick_up_point_id = json_encode($request->pick_up_point_id);
            } else {
                $shop->pick_up_point_id = json_encode(array());
            }
        } elseif ($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')) {
            $shop->facebook = $request->facebook;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        } else {
            $shop->sliders = $request->sliders;
        }

        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
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
        // $shop = Auth::user()->shop;
        $seller = Seller::where('user_id', Auth()->user()->id)->firstorFail();
        if($seller->verification_status==1) {
            $verify_history = Verification::where("user_id", Auth()->user()->id)->orderBy('id', 'DESC')->paginate(15);
            return view('frontend.user.seller.verify_form', compact('verify_history'));
        } else {
            flash(translate('Silahakan verifikasi terlebih dahulu.'))->error();
            return redirect()->route('dashboard');
        }
    }

    public function history(Request $request)
    {
        $shop = Auth::user()->shop;
        $verify_history = Verification::where("user_id", $shop->user_id)->paginate(15);

        return view('frontend.user.seller.verify_form', compact('shop', 'seller', 'verify_history'));
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
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_' . $i]);
            } elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $seller = Auth::user()->seller;
        $seller->verification_info = json_encode($data);
        if ($seller->save()) {
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
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();

            $keys = array_keys(array_column($oldData, 'type'), $element->type);
            foreach ($keys as $kunci) {
                $item = [];

                // if ($i == 23) {
                //     // dd(($oldData[$kunci]->label == $element->label && $request['element_' . $i] == $oldData[$kunci]->value));
                //     dump($kunci, $oldData[$kunci]->label, $element->label, $request['element_' . $i], $oldData[$kunci]->value);
                // }

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

            // dd($item);

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
            }

            array_push($dataChange, $item);
            unset($item['old_value']);
            array_push($data, $item);
            $i++;
        }

        $shop = Auth::user()->shop;
        // $admin = User::where('user_type', 'admin')->get();
        $staff = User::where('user_type', 'staff')->get();
        // dd($admin);
        foreach($staff as $re) {
            Mail::to($re->email)->send(new ShopStatusChange($dataChange, $shop));
        }
        // dd($dataChange);
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
            return redirect()->route('shops.index');
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }
}
