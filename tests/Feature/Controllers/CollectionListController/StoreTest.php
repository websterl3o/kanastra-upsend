<?php

namespace Tests\Feature\Controllers\CollectionListController;

use Tests\TestCase;
use App\Models\CollectionList;
use Illuminate\Http\UploadedFile;
use App\Jobs\ProcessCollectionList;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @description Ele tenta armazenar uma nova lista de cobrança que não esta no formato csv.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_tries_to_store_a_new_collection_list_which_is_not_in_csv_format()
    {
        Bus::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('collection-lists-' . now()->format('Y-m-d') . '.txt', 100, 'text/plain');

        $response = $this->postJson(route('collection-lists.store'), [
            'file' => $file
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('file');

        Storage::disk('local')->assertMissing('collection-lists-' . now()->format('Y-m-d') . "/" . $file->hashName());
        Bus::assertNotDispatched(ProcessCollectionList::class);
    }

    /**
     * @description Ele tenta armazenar uma nova lista de cobrança que não é um arquivo.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_tries_to_store_a_new_collection_list_which_is_not_a_file()
    {
        Bus::fake();
        Storage::fake('local');

        $response = $this->postJson(route('collection-lists.store'), [
            'file' => 'not-a-file'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('file');

        Bus::assertNotDispatched(ProcessCollectionList::class);
    }

    /**
     * @description Ele tenta armazenar uma nova lista de cobrança que é um arquivo csv.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_stores_a_new_collection_list_which_is_a_csv_file()
    {
        Bus::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('collection-lists-' . now()->format('Y-m-d') . '.csv', 100, 'text/csv');

        $response = $this->postJson(route('collection-lists.store'), [
            'file' => $file
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('collection_lists', [
            'original_name' => 'collection-lists-' . now()->format('Y-m-d') . '.csv',
            'path' => 'collection-lists-' . now()->format('Y-m-d') . "/" . $file->hashName(),
        ]);

        $collectionList = CollectionList::first();

        Storage::disk('local')->assertExists('collection-lists-' . now()->format('Y-m-d') . "/" . $file->hashName());
        Bus::assertDispatched(ProcessCollectionList::class, function ($job) use ($collectionList) {
            return $job->collectionList()->is($collectionList);
        });
    }

    /**
     * @description Ele tenta armazenar uma nova lista de cobrança que é um arquivo csv e maior que 200 megabytes.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_tries_to_store_a_new_collection_list_which_is_a_csv_file_and_bigger_than_200_megabytes()
    {
        Bus::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('collection-lists-' . now()->format('Y-m-d') . '.csv', 204801, 'text/csv');

        $response = $this->postJson(route('collection-lists.store'), [
            'file' => $file
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('file');

        Storage::disk('local')->assertMissing('collection-lists-' . now()->format('Y-m-d') . "/" . $file->hashName());
        Bus::assertNotDispatched(ProcessCollectionList::class);
    }
}
