<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use App\Models\CollectionList;
use App\Models\RegisterOfDebt;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterAndSendBilling implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    private CollectionList $collectionList;
    private array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(CollectionList $collectionList, array $data)
    {
        $this->collectionList = $collectionList;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $row) {
            if (Str::lower($row[5]) == 'debtid') {
                continue;
            }

            $register = $this->registerBilling($row, $this->collectionList);
            $this->sendBilling($register);
        }
    }

    protected function registerBilling(array $row, CollectionList $collectionList): RegisterOfDebt
    {
        Log::info("Getting or creating register Billing with uuid {$row[5]}.");
        return RegisterOfDebt::firstOrCreate([
            'uuid' => $row[5],
            'amount' => $row[3],
            'dueDate' => $row[4],
            'name' => $row[0],
            'email' => $row[2],
            'government_id' => $row[1],
            'collectionlist_id' => $collectionList->id,
        ]);
    }

    protected function sendBilling(RegisterOfDebt $register): void
    {
        if ($register->notified_at != null) {
            Log::info("Billing {$register->uuid} already notified.");
            return;
        }

        Log::info("Sending billing {$register->uuid} to {$register->email} with amount {$register->amount} and update notified_at.");
        $register->update(['notified_at' => now()]);
    }
}
