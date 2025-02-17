<?php

namespace Modules\Location\Console\Commands;

use Illuminate\Console\Command;

class LocationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:LocationCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Location Command description';

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
