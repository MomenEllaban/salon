<?php

namespace Modules\Commission\Console\Commands;

use Illuminate\Console\Command;

class CommissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CommissionCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commission Command description';

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
