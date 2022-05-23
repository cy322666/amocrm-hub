<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\Salesforce\Client;
use FuelSdk\ET_Client;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sf:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sfApi = (new Client(Account::query()
            ->where('name', 'salesforce')
            ->first())
        );
        $result = $sfApi->services->update([
            'id' => 1,
            'status' => 'Тест',
        ]);

        dd($result);
    }
}
