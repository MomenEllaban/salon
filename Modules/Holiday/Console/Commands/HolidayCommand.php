<?php

namespace Modules\Holiday\Console\Commands;

use Illuminate\Console\Command;

class HolidayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:HolidayCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Holiday Command description';

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
