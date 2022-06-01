<?php


namespace App\Services\Salesforce\Models;

use App\Models\Account;
use App\Services\Salesforce\Services\Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class Base
{
    protected static string $baseUrl = 'https://avito.my.salesforce.com/';

    public Client $http;
    public $account;

    public function init(Account $account)
    {
        $this->http = new Client();

        $this->account = $account;

        if ($this->account->access_token == null ||
            $this->account->expires_in !== null ||
            $this->account->expires_in > Carbon::now()->subHours(2)->timestamp) {

            Auth::refresh_access($account, $this->http);
        }
        return $this;
    }

    /**
     * @throws GuzzleException
     */
    public function update(array $params): string
    {
        Log::info(__METHOD__, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->account->access_token,
            ],
            'json' => [
                'gigantUpdate' => [
                    'id'     => $params['id'],
                    'status' => $params['status'],
                ]
            ]
        ]);

        $response = $this->http->request('POST', self::$baseUrl.'services/apexrest/gigant', [
            'headers' => [
//                'Content-type'  => 'application/json',
                'Authorization' => 'Bearer '.$this->account->access_token,
            ],
            'json' => [
                'gigantUpdate' => [
                    'id'     => $params['id'],
                    'status' => $params['status'],
                ]
            ]
        ]);

        return $response->getBody()->getContents();
    }
}
