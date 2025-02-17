<?php

namespace Modules\Package\Console\Commands;

use Illuminate\Console\Command;

class PackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:PackageCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Command description';

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
