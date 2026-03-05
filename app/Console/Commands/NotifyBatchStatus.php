<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batch;
use App\Services\BatchNotificationService;

class NotifyBatchStatus extends Command
{
    protected $signature = 'batch:notify-status {batch_id}';
    protected $description = 'Manually trigger status change email for a batch';

    public function handle(BatchNotificationService $notifier)
    {
        $batchId = $this->argument('batch_id');
        $batch = Batch::find($batchId);

        if (!$batch) {
            $this->error("Batch not found!");
            return;
        }

        $this->info("Sending notifications for Batch: {$batch->batch_code} ({$batch->status})...");
        
        $notifier->notifyStatusChange($batch);

        $this->info("Notifications queued successfully.");
    }
}