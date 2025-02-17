<?php

namespace Modules\Earning\Console\Commands;

use Illuminate\Console\Command;

class EarningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:EarningCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Earning Command description';

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
