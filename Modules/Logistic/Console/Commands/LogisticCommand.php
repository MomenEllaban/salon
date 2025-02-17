<?php

namespace Modules\Logistic\Console\Commands;

use Illuminate\Console\Command;

class LogisticCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:LogisticCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logistic Command description';

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
