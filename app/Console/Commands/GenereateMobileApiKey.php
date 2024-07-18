<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenereateMobileApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:apikey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Mobile API Key';

    public function generateToken()
    {
        $randString = Str::upper(Str::random(20));

        return $randString;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info($this->generateToken());
    }
}
