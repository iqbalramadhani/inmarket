<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\IndonesiaCity;
use App\Models\IndonesiaDistrict;
use App\Models\IndonesiaProvince;
use App\Models\IndonesiaVillage;
use App\Models\District;
use App\Models\PostalCode;
use App\Models\RajaOngkirCity;
use App\Models\RajaOngkirProvince;
use App\Models\RajaOngkirSubdistrict;
use App\Models\SubDistrict;
use Illuminate\Support\Facades\DB;

class MigrationDataController extends Controller
{
// update query migration data address    
    public function updateShopAddress()
    {
        Shop::chunk(50, function ($batches) {
            foreach ($batches as $data) {

                if ($data->sub_district != null) {
                    $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($data->sub_district) . '%'])->get();
                    if (count($search_villages) == 1) {

                        $village = $search_villages->first();
                        $subdistrict_id = $village->code;
                        $district_id = $village->district_code;

                        $search_district = IndonesiaDistrict::where('code', $district_id)->first();
                        $search_city = IndonesiaCity::where('code', $search_district->city_code)->first();

                        $city_id = $search_city->code;
                        $province_id = $search_city->province_code;

                    } else if (count($search_villages) > 1) {

                        $search_district = IndonesiaDistrict::where('name', 'like', ['%' . strtoupper($data->district) . '%'])->where('name', $data->district)->first();

                        if ($search_district) {
                            $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($data->sub_district) . '%'])->where('district_code', $search_district->code)->first();
                            if ($search_villages) {
                                $search_city = IndonesiaCity::where('code', $search_district->city_code)->first();
                                $province_id = $search_city->province_code;
                                $city_id = $search_city->code;
                                $district_id = $search_district->code;
                                $subdistrict_id = $search_villages->code;
                            } else {
                                continue;
                            }
                            
                        } else {
                            continue;
                        }

                    }
                } else {
                    continue;
                }
                
                $data->update([
                    'indonesia_province_id' => $province_id,
                    'indonesia_city_id' => $city_id,
                    'indonesia_district_id' => $district_id,
                    'indonesia_subdistrict_id' => $subdistrict_id,
                ]);
            }
        });

        echo 'Done Update SHOPS Table :)';
        exit();
    }

    public function updatePostalCodes()
    {   
        PostalCode::chunk(1000, function ($batches) {
            foreach ($batches as $data) {
                $get_subdistrict = SubDistrict::where('id', $data->kelurahan_id)->first();
                $subdistrict_name = $get_subdistrict->name;

                if (strpos($subdistrict_name, '(') && strpos($subdistrict_name, ')')) {
                    $temp_subdistrict_name = $this->splitWord($subdistrict_name);
                    
                    $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($temp_subdistrict_name['first_word']) . '%'])
                                        ->orWhere('name', 'like', ['%' . strtoupper($temp_subdistrict_name['second_word']) . '%'])->get();

                    if (count($search_villages) == 1) {

                        $village = $search_villages->first();
                        $village_code = $village->code;

                    } else if (count($search_villages) > 1) {

                        $get_district = District::where('id', $get_subdistrict->kecamatan_id)->first();
                        $district_name = $get_district->name;

                        if (strpos($district_name, '(') && strpos($district_name, ')')) {
                            $temp_district_name = $this->splitWord($district_name);
                            $search_district = IndonesiaDistrict::where('name', 'like', ['%' . strtoupper($temp_district_name['first_word']) . '%'])
                                                ->orWhere('name', 'like', ['%' . strtoupper($temp_district_name['second_word']) . '%'])->first();
                            if ($search_district) {
                                $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($temp_subdistrict_name['first_word']) . '%'])
                                                    ->orWhere('name', 'like', ['%' . strtoupper($temp_subdistrict_name['second_word']) . '%'])
                                                    ->where('district_code', $search_district->code)->first();
                                if ($search_villages) {
                                    $village_code = $search_villages->code;
                                } else {
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        } else {
                            $search_district = IndonesiaDistrict::where('name', 'like', ['%' . strtoupper($district_name) . '%'])->first();
                            if ($search_district) {
                                $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($subdistrict_name) . '%'])->where('district_code', $search_district->code)->first();
                                if ($search_villages) {
                                    $village_code = $search_villages->code;
                                } else {
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        }

                    }

                } else {
                    $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($subdistrict_name) . '%'])->get();
                    if (count($search_villages) == 1) {

                        $village = $search_villages->first();
                        $village_code = $village->code;

                    } else if (count($search_villages) > 1) {

                        $get_district = District::where('id', $get_subdistrict->kecamatan_id)->first();
                        $district_name = $get_district->name;

                        $search_district = IndonesiaDistrict::where('name', 'like', ['%' . strtoupper($district_name) . '%'])->first();
                        if ($search_district) {
                            $search_villages = IndonesiaVillage::where('name', 'like', ['%' . strtoupper($subdistrict_name) . '%'])->where('district_code', $search_district->code)->first();
                            if ($search_villages) {
                                $village_code = $search_villages->code;
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }

                    } else {
                        continue;
                    }
                }
   
                $data->update([
                    'village_code' => $village_code,
                ]);
            }
        });

        echo 'Done Update POSTAL_CODES Table :)';
        exit();
    }

    private function splitWord($word){
        // split and get word in ('sentences')
        $word = explode('(', $word);
        $first_word = rtrim($word[0]);
        $second_word = str_replace(')', '', $word[1]);
        return $data = [
            'first_word' => $first_word,
            'second_word' => $second_word,
        ];
    }

    public function updateIndonesiaProvince(){
        ini_set('max_execution_time', 180); //3 minutes
        DB::beginTransaction();
        IndonesiaProvince::chunk(100, function($batches){
            foreach($batches as $data){
                $search_provonces = RajaOngkirProvince::where("province_name",'like',['%' . strtoupper($data->name) . '%'])->first();
                if($search_provonces){
                    $province_id = $search_provonces->province_id;
                }else{
                    continue;
                }
                $data->update(['rajaongkir_provinces_id' => $province_id]);
            }
        });

        echo "done update indonesia porvinces tables";

        IndonesiaCity::chunk(100, function($batches){
            foreach($batches as $data){
                $search_city = RajaOngkirCity::where('city_name','like',['%' . strtoupper($data->name) . '%'])->first();
                if($search_city){
                    $city_id = $search_city->city_id ;
                }else{
                    continue;
                }
                $data->update(['rajaongkir_cities_id' => $city_id]);
            }
        });

        DB::commit();

        echo "done update indonesia city tables";
    }

    public function updateIndonesiaDistrict(){
        DB::beginTransaction();
        IndonesiaDistrict::chunk(100, function($batches){
            foreach($batches as $data){
                $search_distirct = RajaOngkirSubdistrict::where('subdistrict_name','like',['%' . strtoupper($data->name) . '%'])->first();
                if($search_distirct){
                    $subdistrict_id  = $search_distirct->subdistrict_id ;
                }else{
                    continue;
                }
                $data->update(['rajaongkir_subdistricts_id' => $subdistrict_id ]);
            }
        });

        DB::commit();

        echo "done update indonesia district tables";
    }

}
