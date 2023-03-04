<?php

namespace App\Services;

class RajaOngkirService
{
    protected $api_key;

    public function __construct(){
        $this->api_key = env('RAJA_ONGKIR_API_KEY');
    }

    public function getProvinces()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: $this->api_key"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    public function getCities($province_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/city?province=$province_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: $this->api_key"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    public function getSubDistricts($city_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=$city_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "key: $this->api_key"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function getCost($from_id, $to_id, $weight, $courier, $originType='city')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pro.rajaongkir.com/api/cost',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "origin=$from_id&originType=$originType&destination=$to_id&destinationType=$originType&weight=$weight&courier=$courier",
            CURLOPT_HTTPHEADER => array(
                'content-type: application/x-www-form-urlencoded',
                "key: $this->api_key"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response, true);
        if($result['rajaongkir']['status']['code'] === 400){
            if(isset($this->translateErrorGetCost()[$result['rajaongkir']['status']['description']])){
                $status = 400;
                $data = $this->translateErrorGetCost()[$result['rajaongkir']['status']['description']];
            }else{
                $status = 400;
                $data = $this->translateErrorGetCost()['undefined'];
            }
        }else{
            $status = 200;
            $data = $result;
        }

        if($status === 200){
            return $result;
        }

        return [
            'status' => $status,
            'data' => $data
        ];
    }

    public function translateErrorGetCost()
    {
        return  [
            'Bad request. Weight harus diisi.' => 'Produk tidak memiliki attribute berat, tidak bisa dilanjutkan, harap hubungi seller',
            'Bad request. Origin harus diisi' => 'Seller belum memiliki alamat, harap hubungi seller',
            'Bad request. Destination harus diisi' => 'Pembeli mohon sertakan alamat pengiriman',
            'undefined' => 'Gagal mendapatkan ongkos kirim'
        ];
    }

    public function track($resi_number, $courier)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pro.rajaongkir.com/api/waybill',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "waybill=$resi_number&courier=$courier",
            CURLOPT_HTTPHEADER => array(
                'content-type: application/x-www-form-urlencoded',
                "key: $this->api_key"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }
}