<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PriceFetcherService;

class UpdatePrices extends Command
{
    protected $signature = 'prices:update';
    protected $description = 'Update prices for subscriptions and notify users if they change';

    private $priceFetcherService;

    public function __construct(PriceFetcherService $priceFetcherService)
    {
        parent::__construct();
        $this->priceFetcherService = $priceFetcherService;
    }

    public function handle()
    {
        $this->info('Starting price update process...');
        $this->priceFetcherService->updatePrices();
        $this->info('Price update process completed.');
    }
}


