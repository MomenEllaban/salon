<?php

namespace Modules\QuickBooking\Console\Commands;

use Illuminate\Console\Command;

class QuickBookingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:QuickBookingCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'QuickBooking Command description';

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
