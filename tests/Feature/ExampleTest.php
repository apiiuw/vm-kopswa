<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_vehicles_and_financial(): void
    {
        $user = User::first();
        $this->actingAs($user)->get('/vehicles')->assertStatus(200);
        $this->actingAs($user)->get('/financial')->assertStatus(200);
        $this->actingAs($user)->get('/maintenance')->assertStatus(200);
        $this->actingAs($user)->get('/documents')->assertStatus(200);
        $this->actingAs($user)->get('/insurance')->assertStatus(200);
    }
}
