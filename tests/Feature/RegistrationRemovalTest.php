<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationRemovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // With no accounts, /login redirects to /setup; create one so the
        // login page itself renders (registration must still be absent).
        User::create([
            'username' => 'existing', 'password' => 'password',
            'firstName' => 'Existing', 'lastName' => 'User',
            'roleID' => Role::where('roleName', 'Admin')->value('roleID'),
        ]);
    }

    public function test_register_route_returns_404(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(404);
    }

    public function test_login_page_does_not_contain_register_link(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('Register');
    }
}
