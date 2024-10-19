<?php

namespace App\Jobs;

use App\Models\CollectionList;
use App\Models\RegisterOfDebt;
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
            if ($row[5] == 'debtID') {
                continue;
            }

            $register = RegisterOfDebt::firstOrCreate([
                'uuid' => $row[5],
                'amount' => $row[3],
                'dueDate' => $row[4],
                'name' => $row[0],
                'email' => $row[2],
                'government_id' => $row[1],
                'collectionlist_id' => $this->collectionList->id,
            ]);

            if ($register->notified_at === null) {
                // Mail::to($register->email)->send(new Billing($register));
                // $register->update(['notified_at' => now()]);
            }
        }
    }
}
