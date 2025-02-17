<?php

namespace Modules\Promotion\Console\Commands;

use Illuminate\Console\Command;

class PromotionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:PromotionCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promotion Command description';

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
