<?php

namespace Modules\Product\Console\Commands;

use Illuminate\Console\Command;

class ProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ProductCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product Command description';

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
