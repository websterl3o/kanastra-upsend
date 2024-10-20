<?php

namespace Tests\Feature\Controllers\CollectionListController;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CreateTest extends TestCase
{
    /**
     * @description Testa se o acesso a view pelo método GET está funcionando
     */
    #[Test]
    public function it_access_view(): void
    {
        $response = $this->get(route('collection-lists.create'));

        $response->assertStatus(200);
    }
}
