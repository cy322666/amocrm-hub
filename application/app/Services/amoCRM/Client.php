<?php

namespace App\Services\amoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\AmoCRMApiClientFactory;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Models\Account;
use League\OAuth2\Client\Token\AccessToken;

class Client
{
    /**
     * @throws AmoCRMoAuthApiException
     */
    public function getInstance(Account $account): AmoCRMApiClient
    {
        $apiClient = (new AmoCRMApiClientFactory(
            new OauthEloquentConfig($account),
            new OauthEloquentService($account))
        )->make()
         ->setAccountBaseDomain($account->subdomain);

        if($account->access_token == null) {

            $access_token = $apiClient
                ->getOAuthClient()
                ->getAccessTokenByCode($account->code);

            if (!$access_token->hasExpired()) {

                $account->access_token  = $access_token->getToken();
                $account->refresh_token = $access_token->getRefreshToken();
                $account->expires_in    = $access_token->getExpires();
//                $account->work = 1;
                $account->save();
            }
        } else {

            $access_token = new AccessToken([
                'access_token'  => $account->access_token,
                'refresh_token' => $account->refresh_token,
                'expires_in'    => $account->expires_in,
            ]);
        }
        $apiClient->setAccessToken($access_token);

        return $apiClient;
    }

    public function checkAuth(Account $account) : bool
    {
        if ($account->work == 0 ||
            $account->client_id == null ||
            $account->subdomain == null ||
            $account->access_token  == null ||
            $account->client_secret == null) {

            return false;
        } else
            return true;
    }
}
