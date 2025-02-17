<?php

namespace Modules\Employee\Console\Commands;

use Illuminate\Console\Command;

class EmployeeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:EmployeeCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee Command description';

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
