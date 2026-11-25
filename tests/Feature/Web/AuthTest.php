<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('vacancies.index'))->assertRedirect(route('login'));
        $this->get(route('candidates.index'))->assertRedirect(route('login'));
    }

    public function test_login_page_is_displayed(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Entrar');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('vacancies.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login'), ['email' => $user->email, 'password' => 'errada'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_register(): void
    {
        $this->post(route('register'), [
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'password' => 'senhaforte123',
            'password_confirmation' => 'senhaforte123',
        ])->assertRedirect(route('vacancies.index'));

        $this->assertDatabaseHas('users', ['email' => 'maria@example.com']);
        $this->assertAuthenticated();
    }

    public function test_register_requires_password_confirmation(): void
    {
        $this->post(route('register'), [
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'password' => 'senhaforte123',
            'password_confirmation' => 'outra',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
