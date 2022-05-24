<?php

namespace App\Http\Controllers\Salesforce;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Salesforce\Hook;
//use App\Services\amoCRM\Actions\AvitoHookSender;
use App\Services\amoCRM\Actions\SalesforceHookSender;
use App\Services\amoCRM\Client as amoApi;
use App\Services\Salesforce\Client as Avito;
use App\Services\Salesforce\Actions\amoCRMHookSender;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HookController extends Controller
{
    public function salesforce(Request $request, amoApi $client): \Illuminate\Http\Response|Application|ResponseFactory
    {
        Log::info(__METHOD__, $request->toArray());

        try {
            $hook = Hook::query()->create($request->toArray());

            $amoApi = $client->getInstance(Account::query()
                ->where('name', 'amocrm')
                ->first()
            );

            (new SalesforceHookSender($hook, $amoApi))->send();

        } catch (\Exception $exception) {

            Log::error(__METHOD__.' : '.$exception->getMessage());
        }

        return response([
            'code'   => 200,
            'result' => true,
        ]);
    }

    public function amocrm(Request $request, Avito $sfApi)
    {
        Log::info(__METHOD__, $request->toArray());

        $leadId = $request->leadId;

        $hook = Hook::query()
            ->where('lead_id', $leadId)
            ->first();

        if ($hook) {
            $result = (new amoCRMHookSender($hook, $sfApi))->send();

        } else
            Log::alert(__METHOD__.' : hook for lead_id not found '.$leadId);
    }
}
