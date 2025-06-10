<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Addemployer extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_example(): void
    {
        $user = User::query()->where('roles_idRole', 2)->first();
        $roleId = Role::query()->where('value', 'Менеджер')->first()->id;
        $response = $this->actingAs($user)->post('/api/personal', [
            'name' => 'Никита',
            'surname' => 'Ляхов',
            'thirdname' => 'Романович',
            'login' => 'Edinop0jek',
            'password' => 'qweqwe123',
            'phone' => '+7800123456',
            'roles_idRole' => $roleId
        ]);

        $response->assertStatus(200);
    }
}
