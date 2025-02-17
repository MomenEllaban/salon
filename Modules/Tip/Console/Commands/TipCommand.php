<?php

namespace Modules\Tip\Console\Commands;

use Illuminate\Console\Command;

class TipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TipCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tip Command description';

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
