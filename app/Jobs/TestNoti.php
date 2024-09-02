<?php

namespace App\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TestNoti implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        sleep(3);
        Log::info('TestNoti: ' . $this->message);
    }
}
