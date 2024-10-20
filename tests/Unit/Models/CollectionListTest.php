<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CollectionList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollectionListTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @description Verifica se quando um CollectionList for criado se ele tem o notified_at null.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_collection_list_has_correct_attributes_when_created()
    {
        $collectionList = CollectionList::create([
            'original_name' => 'file.csv',
            'name' => 'file.csv',
            'path' => 'collection-lists-' . now()->format('Y-m-d') . '/file.csv',
        ]);

        $this->assertEquals('file.csv', $collectionList->original_name);
        $this->assertEquals('file.csv', $collectionList->name);
        $this->assertEquals('collection-lists-' . now()->format('Y-m-d') . '/file.csv', $collectionList->path);
        $this->assertNull($collectionList->processed_at);
    }

    /**
     * @description Verifica se o atributo processed_at é convertido para um objeto DateTime.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_processed_at_is_converted_to_datetime()
    {
        $collectionList = CollectionList::create([
            'original_name' => 'file.csv',
            'name' => 'file.csv',
            'path' => 'collection-lists-' . now()->format('Y-m-d') . '/file.csv',
            'processed_at' => now(),
        ]);

        $this->assertInstanceOf(\DateTime::class, $collectionList->processed_at);
    }

    /**
     * @description Verifica a factory de CollectionList.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_collection_list_factory()
    {
        $collectionList = CollectionList::factory()->create();

        $this->assertNotNull($collectionList->id);
        $this->assertNotNull($collectionList->original_name);
        $this->assertNotNull($collectionList->name);
        $this->assertNotNull($collectionList->path);
        $this->assertNull($collectionList->processed_at);
    }
}
