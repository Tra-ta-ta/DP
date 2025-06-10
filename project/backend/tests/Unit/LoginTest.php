<?php

namespace Tests\Unit;

use Tests\TestCase;


class LoginTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $response = $this->post('/login', [
            'login' => 'Edinop0jek01',
            'password' => 'qweqwe123'
        ]);

        $response->assertStatus(200);
    }
}
