<?php

namespace App\Console\Commands;

use App\Services\GoogleFitService;
use Illuminate\Console\Command;

class RefreshGoogleFit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mygooglefit:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Access Token Google Fit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        GoogleFitService::make()->refreshAccessToken();
    }
}
