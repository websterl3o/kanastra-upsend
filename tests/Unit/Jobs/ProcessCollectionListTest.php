<?php

namespace Tests\Unit\Jobs;

use Mockery;
use Tests\DataBaseTestCase;
use App\Models\CollectionList;
use PHPUnit\Event\Runtime\PHP;
use Illuminate\Http\UploadedFile;
use App\Jobs\ProcessCollectionList;
use Illuminate\Support\Facades\Bus;
use App\Jobs\RegisterAndSendBilling;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class ProcessCollectionListTest extends DataBaseTestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @description Testa se a ProcessCollectionList processa um arquivo vazio.
     */
    #[RunInSeparateProcess]
    #[Test]
    public function it_processes_an_empty_file(): void
    {
        Mockery::close();
        $this->refreshDatabase();

        $file_name = 'input-empty.csv';
        $path = 'tests/testdata/' . $file_name;
        $collectionList = CollectionList::factory()->create([
            'original_name' => $file_name,
            'path' => $path,
        ]);

        $job = Mockery::mock(ProcessCollectionList::class, [$collectionList])->makePartial();
        $job->shouldReceive('openFile')
            ->once()
            ->andReturn(new \SplFileObject(base_path($path)));

        Bus::fake();

        $job->handle();

        Bus::assertNotDispatched(RegisterAndSendBilling::class);
        $this->assertNotNull($collectionList->processed_at);
    }

    /**
     * @description Testa se a ProcessCollectionList processa um arquivo com 2051 linhas.
     */
    #[RunInSeparateProcess]
    #[Test]
    public function it_processes_a_file_with_2051_lines(): void
    {
        Mockery::close();
        $this->refreshDatabase();

        $file_name = 'input-xs.csv';
        $path = 'tests/testdata/' . $file_name;
        $collectionList = CollectionList::factory()->create([
            'original_name' => $file_name,
            'path' => $path,
        ]);

        $job = Mockery::mock(ProcessCollectionList::class, [$collectionList])->makePartial();
        $job->shouldReceive('openFile')
            ->once()
            ->andReturn(new \SplFileObject(base_path($path)));

        Bus::fake();

        $job->handle();

        Bus::assertDispatched(RegisterAndSendBilling::class, 5);
        $this->assertNotNull($collectionList->processed_at);
    }

    /**
     * @description Testa se a ProcessCollectionList processa um arquivo com 9579 linhas.
     */
    #[RunInSeparateProcess]
    #[Test]
    public function it_processes_a_file_with_9579_lines(): void
    {
        Mockery::close();
        $this->refreshDatabase();

        $file_name = 'input-small.csv';
        $path = 'tests/testdata/' . $file_name;
        $collectionList = CollectionList::factory()->create([
            'original_name' => $file_name,
            'path' => $path,
        ]);

        $job = Mockery::mock(ProcessCollectionList::class, [$collectionList])->makePartial();
        $job->shouldReceive('openFile')
            ->once()
            ->andReturn(new \SplFileObject(base_path($path)));

        Bus::fake();

        $job->handle();

        Bus::assertDispatched(RegisterAndSendBilling::class, 20);
        $this->assertNotNull($collectionList->processed_at);
    }

    /**
     * @description Testa se a função processRegisterAndSendBilling se passar array vazio chama o job de RegisterAndSendBilling.
     */
    #[Test]
    public function it_calls_RegisterAndSendBilling_job_when_processRegisterAndSendBilling_receives_empty_array(): void
    {
        $collectionList = CollectionList::factory()->create();
        $job = new ProcessCollectionList($collectionList);

        Bus::fake();

        $job->processRegisterAndSendBilling([]);

        Bus::assertNotDispatched(RegisterAndSendBilling::class);
    }

    /**
     * @description Testa se a função processRegisterAndSendBilling chama o job de RegisterAndSendBilling passando array com itens.
     */
    #[Test]
    public function it_calls_RegisterAndSendBilling_job_when_processRegisterAndSendBilling_receives_array_with_items(): void
    {
        $collectionList = CollectionList::factory()->create();
        $job = new ProcessCollectionList($collectionList);

        Bus::fake();

        $job->processRegisterAndSendBilling([['item1', 'item2']]);

        Bus::assertDispatched(RegisterAndSendBilling::class);
    }

    /**
     * @description Testa se a ProcessCollectionList processa um arquivo com menos de 6 colunas.
     */
    #[RunInSeparateProcess]
    #[Test]
    public function it_skips_rows_with_less_than_six_columns(): void
    {
        Mockery::close();
        $this->refreshDatabase();

        $file_name = 'input-invalid.csv';
        $path = 'tests/testdata/' . $file_name;
        $collectionList = CollectionList::factory()->create([
            'original_name' => $file_name,
            'path' => $path,
        ]);

        $job = Mockery::mock(ProcessCollectionList::class, [$collectionList])->makePartial();
        $job->shouldReceive('openFile')
            ->once()
            ->andReturn(new \SplFileObject(base_path($path)));

        Bus::fake();

        $job->handle();

        Bus::assertNotDispatched(RegisterAndSendBilling::class);
        $this->assertNotNull($collectionList->processed_at);
    }

    /**
     * @description Testa se a ProcessCollectionList lança uma exceção quando o arquivo não é encontrado.
     */
    #[Test]
    public function it_throws_exception_when_file_not_found(): void
    {
        $collectionList = CollectionList::factory()->create([
            'path' => 'non-existent-file.csv',
        ]);

        $job = new ProcessCollectionList($collectionList);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not found. Collection list ID: ' . $collectionList->id);

        $job->openFile();
    }

    /**
     * @description Testa se a função openFile retorna um objeto SplFileObject.
     */
    #[Test]
    public function it_returns_a_SplFileObject(): void
    {
        $this->markTestSkipped(
            'Teste para rever, o arquivo não é criado no disco e causa exception no job.',
        );

        Storage::fake();
        $fileOriginalName = 'input.csv';
        $fileContent = 'content';

        $file = UploadedFile::fake()->create($fileOriginalName, 1024, 'text/csv', $fileContent);

        $path = $file->storeAs('private', 'collection-lists-' . now()->format('Y-m-d'));

        $collectionList = CollectionList::factory()->create([
            'original_name' => $fileOriginalName,
            'name' => $file->hashName(),
            'path' => $path,
        ]);

        $job = new ProcessCollectionList($collectionList);

        $file = $job->openFile();

        $this->assertInstanceOf(\SplFileObject::class, $file);
    }
}
