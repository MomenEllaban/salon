<?php

namespace Modules\Booking\Console\Commands;

use Illuminate\Console\Command;

class BookingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:BookingCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Booking Command description';

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
