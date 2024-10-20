<?php

namespace Tests\Unit\Controllers\CollectionListController;

use Mockery;
use Tests\TestCase;
use App\Models\CollectionList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Jobs\ProcessCollectionList;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreCollectionListRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @description O controller armazena uma nova lista de cobrança no formato csv e disparar um job para processá-la.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_stores_a_new_collection_list_in_csv_format_and_dispatches_a_job_to_process_it()
    {
        Bus::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('collection-lists-' . now()->format('Y-m-d') . '.csv', 100, 'text/csv');

        $request = StoreCollectionListRequest::create('/collection-lists', 'POST', [], [], ['file' => $file]);

        $controller = new \App\Http\Controllers\CollectionListController();

        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $collectionList = CollectionList::first();

        $this->assertNotNull($collectionList);

        $this->assertEquals($file->getClientOriginalName(), $collectionList->original_name);
        $this->assertEquals($file->hashName(), $collectionList->name);
        $this->assertEquals('collection-lists-' . now()->format('Y-m-d') . '/' . $file->hashName(), $collectionList->path);

        Storage::disk('local')->assertExists('collection-lists-' . now()->format('Y-m-d') . "/" . $file->hashName());

        Bus::assertDispatched(ProcessCollectionList::class, function ($job) use ($collectionList) {
            return $job->collectionList()->is($collectionList);
        });
    }

    /**
     * @description Ele tenta armazenar uma nova lista de cobrança, porem uma exception é lançada quando tenta dar o CollectionList::create.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_stores_a_new_collection_list_but_an_exception_is_thrown_when_trying_to_create_the_collection_list()
    {
        Bus::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('collection-lists-' . now()->format('Y-m-d') . '.csv', 100, 'text/csv');

        // Mock the CollectionList model
        $collectionListMock = \Mockery::mock('overload:App\Models\CollectionList');
        $collectionListMock->shouldReceive('create')->andThrow(new \Exception('Error creating collection list'));


        $request = StoreCollectionListRequest::create('/collection-lists', 'POST', [], [], ['file' => $file]);

        $controller = new \App\Http\Controllers\CollectionListController();

        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @description Ele tenta armazenar uma nova lista de cobrança, mas uma exception é lançada quando tenta salvar o arquivo.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_stores_a_new_collection_list_but_an_exception_is_thrown_when_trying_to_save_the_file()
    {
        Bus::fake();
        Storage::fake('local');

        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('store')->andThrow(new \Exception('Error saving file'));

        $request = StoreCollectionListRequest::create('/collection-lists', 'POST', [], [], ['file' => $mockFile]);

        $controller = new \App\Http\Controllers\CollectionListController();

        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
