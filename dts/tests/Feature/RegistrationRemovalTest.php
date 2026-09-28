<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationRemovalTest extends TestCase
{
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
