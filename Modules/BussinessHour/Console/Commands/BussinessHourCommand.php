<?php

namespace Modules\BussinessHour\Console\Commands;

use Illuminate\Console\Command;

class BussinessHourCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:BussinessHourCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'BussinessHour Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
