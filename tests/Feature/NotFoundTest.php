<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFoundTest extends TestCase
{

    public function testBasicNotFound()
    {
        $response = $this->getJson('/api/not/found/');
        $response->assertStatus(404);
    }

}
