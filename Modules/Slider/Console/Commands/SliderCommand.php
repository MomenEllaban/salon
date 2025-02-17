<?php

namespace Modules\Slider\Console\Commands;

use Illuminate\Console\Command;

class SliderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SliderCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Slider Command description';

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
