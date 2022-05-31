<?php


namespace App\Services\Salesforce\Services;

use App\Models\Account;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Auth
{
    public static function refresh_access(Account &$account, $http): bool|string
    {
        $response = $http->request('POST', 'https://avito.lightning.force.com/services/oauth2/token', [
            'headers' => [
                'Content-type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => $account->client_id,
                'client_secret' => $account->client_secret,
                'username' => env('SF_USERNAME'),
                'password' => env('SF_PASSWORD'),
            ]
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        if($response->getStatusCode() == 200) {

            Log::info('refresh', $body);

            $account->access_token = $body['access_token'];
            $account->expires_in   = $body['issued_at'];
            $account->save();

            return true;
        } else {

            Log::error( __METHOD__ .' status : '. $response->getStatusCode(), $body);

            return false;
        }
    }
}
