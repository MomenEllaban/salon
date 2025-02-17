<?php

namespace Modules\MenuBuilder\Console\Commands;

use Illuminate\Console\Command;

class MenuBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MenuBuilderCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MenuBuilder Command description';

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
