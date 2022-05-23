<?php


namespace App\Services\Salesforce;

use App\Models\Account;
use App\Services\Salesforce\Models\Base;

class Client
{
    public Base $services;

    public function __construct(Account $account)
    {
        $this->services = (new Base)->init($account);
    }
}
