<?php

namespace App\Services\Salesforce\Actions;

use App\Models\Account;
use App\Models\Salesforce\Status;
use App\Services\Salesforce\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class amoCRMHookSender
{
    private Model $hook;
    private Client $avito;//TODO ne avito!

    public function __construct(Model $model, Client $avito)
    {
        $this->hook  = $model;
        $this->avito = $avito;
    }

    public function send()
    {
        $sfApi = (new Client(Account::query()
            ->where('name', 'salesforce')
            ->first())
        );

        $response = $sfApi->services->update([
            'id'     => $this->hook->salesforce_id,
            'status' => Status::query()
                ->where('status_id', $this->hook->status_id)
                ->firstOrFail()
                ->status_name,
        ]);

        Log::info(__METHOD__.' : '.$response);
    }
}
