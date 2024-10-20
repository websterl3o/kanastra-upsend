<?php

namespace App\Jobs;

use App\Models\CollectionList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessCollectionList implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    protected CollectionList $collectionList;

    /**
     * Create a new job instance.
     */
    public function __construct(CollectionList $collectionList)
    {
        $this->collectionList = $collectionList;
    }

    public function collectionList(): CollectionList
    {
        return $this->collectionList;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Start - Processing collection list {$this->collectionList->id}");

        $chunkSize = 500;
        $chunk = [];
        $packages = [];

        $file = $this->openFile();

        while (($data = $file->fgetcsv()) !== false) {
            if (count($data) < 6) {
                continue;
            }

            $chunk[] = $data;

            if (count($chunk) === $chunkSize) {
                $packages[] = $chunk;
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            $packages[] = $chunk;
        }

        array_walk($packages, function ($package) {
            $this->processRegisterAndSendBilling($package);
        });

        $this->collectionList->update(['processed_at' => now()]);

        Log::info("End - Processing collection list {$this->collectionList->id}");
    }

    public function processRegisterAndSendBilling(array $chunk): void
    {
        if (count($chunk) === 0) {
            return;
        }

        RegisterAndSendBilling::dispatch($this->collectionList, $chunk);
    }

    public function openFile(): \SplFileObject
    {
        if (!Storage::exists(storage_path('app/private/' . $this->collectionList->path))) {
            Log::error('File not found. Collection list ID: ' . $this->collectionList->id);
            throw new \Exception('File not found. Collection list ID: ' . $this->collectionList->id);
        }

        $file = new \SplFileObject(storage_path('app/private/' . $this->collectionList->path));
        $file->setFlags(\SplFileObject::READ_CSV);

        return $file;
    }
}
