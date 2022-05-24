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
    private Client $salesforce;

    public function __construct(Model $model, Client $salesforce)
    {
        $this->hook = $model;
        $this->salesforce = $salesforce;
    }

    public function send()
    {
        try {
            $response = $this->salesforce->services->update([
                'id'     => $this->hook->salesforce_id,
                'status' => Status::query()
                    ->where('status_id', $this->hook->status_id)
                    ->firstOrFail()
                    ->status_name,
            ]);

            Log::info(__METHOD__.' : '.$response);

        } catch (\Throwable $exception) {

            Log::error(__METHOD__. ' : '. $exception->getMessage());
        }
    }
}
