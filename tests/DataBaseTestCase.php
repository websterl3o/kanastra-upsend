<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class DataBaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function refreshDatabase()
    {
        $this->artisan('migrate:fresh');
        $this->seed();
    }
}
