<?php

namespace Modules\Customer\Console\Commands;

use Illuminate\Console\Command;

class CustomerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CustomerCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Customer Command description';

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
