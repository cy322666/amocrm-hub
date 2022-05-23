<?php

namespace App\Services\amoCRM;

use AmoCRM\OAuth\OAuthServiceInterface;
use App\Models\Account;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OauthEloquentService implements OAuthServiceInterface
{
    private Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function saveOAuthToken(AccessTokenInterface $accessToken, string $baseDomain): void
    {
        $this->account->access_token  = $accessToken->getToken();
        $this->account->expires_in    = $accessToken->getExpires();
        $this->account->refresh_token = $accessToken->getRefreshToken();
        $this->account->save();
    }
}
