<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AddemployerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $user = User::query()->where('roles_idRole', 2)->first();
        $roleId = Role::query()->where('value', 'Менеджер')->first();
        $response = $this->actingAs($user)->post('/api/personal', [
            'name' => 'Никита',
            'surname' => 'Ляхов',
            'thirdname' => 'Романович',
            'login' => 'Edinop0jek',
            'password' => 'qweqwe123',
            'phone' => '+7800123456',
            'role' => $roleId->id
        ]);

        $response->assertStatus(200);
    }
}
