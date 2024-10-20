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
    protected CollectionList $collectionList;
    protected array $data;

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

    protected function registerBilling(array $row): RegisterOfDebt
    {
        Log::info("Searching for register Billing with uuid {$row[5]}.");
        $register = RegisterOfDebt::find($row[5]);

        if ($register == null) {
            Log::info("-- Creating register Billing with uuid {$row[5]}.");
            $register = RegisterOfDebt::create([
                'uuid' => $row[5],
                'email' => $row[2],
                'government_id' => $row[1],
                'amount' => $row[3],
                'dueDate' => $row[4],
                'name' => $row[0],
                'collectionlist_id' => $this->collectionList->id,
            ]);
        }

        return $register;
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
