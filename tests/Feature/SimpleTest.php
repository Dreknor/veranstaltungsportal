<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_test()
    {
        $this->assertTrue(true);
    }
}

