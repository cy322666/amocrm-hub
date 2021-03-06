<?php

namespace App\Http\Controllers\Salesforce;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Salesforce\Hook;
use App\Services\amoCRM\Actions\SalesforceHookSender;
use App\Services\amoCRM\Client as amoApi;
use App\Services\Salesforce\Client;
use App\Services\Salesforce\Client as Salesforce;
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
            $hook = Hook::query()->create([
                "company"   => $request->company,
                "phone"     => $request->phone,
                "email"     => $request->email,
                "manager"   => $request->manager,
                "salesforce_id" => $request->salesforceId,
                "comment"   => $request->comment,
                "email_manager" => $request->email_manager,
                "name"      => $request->name,
                "position"  => $request->position,
            ]);

            $amoApi = $client->getInstance(Account::query()
                ->where('name', 'amocrm')
                ->first()
            );

            (new SalesforceHookSender($hook, $amoApi))->send();

        } catch (\Throwable $exception) {

            Log::error(__METHOD__.' : '.$exception->getMessage());
        }

        return response([
            'code'   => 200,
            'result' => true,
        ]);
    }

    public function amocrm(Request $request)
    {
        $requestData = $request->toArray();

        if ($requestData['leads']['status'][0]['pipeline_id'] !== env('AMOCRM_PIPELINE_ID')) {

            Log::alert(__METHOD__.' : pipeline != '.env('AMOCRM_PIPELINE_ID').' lead id '.$requestData['leads']['status'][0]['id']);

            return;
        }

        Log::info(__METHOD__, $request->toArray());

        $leadId   = $requestData['leads']['status'][0]['id'];
        $statusId = $requestData['leads']['status'][0]['status_id'];

        $hook = Hook::query()
            ->where('lead_id', $leadId)
            ->first();

        if ($hook) {
            $hook->status_id = $statusId;
            $hook->save();

            $account = Account::query()
                ->where('name', 'salesforce')
                ->first();

            $sfApi = new Client($account);

            (new amoCRMHookSender($hook->refresh(), $sfApi))->send();

        } else
            Log::alert(__METHOD__.' : hook for lead_id not found '.$leadId);
    }
}
