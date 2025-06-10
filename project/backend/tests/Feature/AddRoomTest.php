<?php

namespace Tests\Feature;

use App\Models\TypeRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddRoomTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $user = User::query()->where('roles_idRole', 2)->first();
        $id = TypeRoom::query()->where('typeRoom', 'Делюкс')->first()->id;
        $response = $this->actingAs($user)->post('/api/room', [
            'number' => '305',
            'type' => $id
        ]);

        $response->assertStatus(200);
    }
}
