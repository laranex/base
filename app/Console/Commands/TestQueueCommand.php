<?php

namespace App\Console\Commands;

use App\Jobs\TestNoti;
use Illuminate\Console\Command;

class TestQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:queue {--message=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = $this->option('message');
        $this->info('Test Queue');

        // loop 3 times
        for ($i = 0; $i < 3; $i++) {
            TestNoti::dispatch($message . "-" . $i)->onQueue('default');
        }

        $this->info('Test Queue Done');
    }
}
