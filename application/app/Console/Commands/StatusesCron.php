<?php

namespace App\Console\Commands;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Models\Account;
use App\Models\Salesforce\Status;
use App\Services\amoCRM\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;

class StatusesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statuses:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get and save db statuses for amoCRM';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws AmoCRMoAuthApiException|AmoCRMMissedTokenException|DdException
     */
    public function handle()
    {
        $amoApi = (new Client())
            ->getInstance(Account::query()
                ->where('name', 'amocrm')
                ->first()
            );

        $pipelinesService = $amoApi->pipelines();

        try {
            $pipelineCollection = $pipelinesService->getOne(env('AMOCRM_PIPELINE_ID'));

            Status::query()->truncate();

            foreach ($pipelineCollection->getStatuses() as $status) {

                Status::query()->create([
                    'status_id'   => $status->id,
                    'status_name' => $status->name,
                ]);
            }
        } catch (AmoCRMApiException $exception) {

            Log::error(__METHOD__.' : '.$exception->getMessage());
        }
    }
}
