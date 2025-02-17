<?php

namespace Modules\LikeModule\Console\Commands;

use Illuminate\Console\Command;

class LikeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:LikeModuleCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'LikeModule Command description';

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
