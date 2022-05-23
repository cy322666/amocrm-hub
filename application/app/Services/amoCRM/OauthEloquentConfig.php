<?php

namespace App\Services\amoCRM;

use AmoCRM\OAuth\OAuthConfigInterface;
use App\Models\Account;

class OauthEloquentConfig implements OAuthConfigInterface
{
    private Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getIntegrationId(): string
    {
        return $this->account->client_id;
    }

    public function getSecretKey(): string
    {
        return $this->account->client_secret;
    }

    public function getRedirectDomain(): string
    {
        return $this->account->redirect_uri;
    }
}
