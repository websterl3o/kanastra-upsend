<?php

namespace App\Jobs;

use App\Models\CollectionList;
use App\Models\RegisterOfDebt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCollectionList implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    private CollectionList $collectionList;

    /**
     * Create a new job instance.
     */
    public function __construct(CollectionList $collectionList)
    {
        $this->collectionList = $collectionList;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Start - Processing collection list {$this->collectionList->id}");

        $chunkSize = 500;  // Definindo o tamanho do chunk
        $chunk = [];       // Armazena as linhas de cada chunk
        $row = 0;          // Contador de linha

        $file = new \SplFileObject(storage_path('app/private/' . $this->collectionList->path));
        $file->setFlags(\SplFileObject::READ_CSV);

        while (!$file->eof()) {
            $data = $file->fgetcsv();
            $chunk[] = $data;
            $row++;

            if ($row % $chunkSize === 0) {
                RegisterAndSendBilling::dispatch($this->collectionList, $chunk);
                $chunk = [];
            }
        }

        $this->collectionList->update(['processed_at' => now()]);

        Log::info("End - Processing collection list {$this->collectionList->id}");
    }
}
