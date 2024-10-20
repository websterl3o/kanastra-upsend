<?php

namespace Tests\Unit\Jobs;

use Mockery;
use Tests\DataBaseTestCase;
use App\Models\CollectionList;
use App\Jobs\ProcessCollectionList;
use Illuminate\Support\Facades\Bus;
use App\Jobs\RegisterAndSendBilling;
use PHPUnit\Framework\Attributes\Test;
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
}
