<?php

namespace App\Services;

use Illuminate\Http\Request;

class OYIService
{
    public static function accountInquiry(string $bank_code, string $account_number)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('OYID_BASEURL') . '/api/account-inquiry', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            'json' => [
                "bank_code" => $bank_code,
                "account_number" => $account_number,
            ],
            'timeout' => 50,
        ]);

        return (
            json_decode((string) $response->getBody())
        );
    }
}