<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_admin_must_enter_the_code_received_by_email(): void
    {
        Notification::fake();
        $admin = $this->userWithRole('admin');

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();

        $code = null;
        Notification::assertSentTo($admin, TwoFactorCodeNotification::class, function ($n) use (&$code) {
            $code = $n->code;

            return true;
        });

        $this->get(route('two-factor.challenge'))->assertOk()->assertSee($admin->email);
        $this->post(route('two-factor.verify'), ['code' => $code === '000000' ? '111111' : '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();

        $this->post(route('two-factor.verify'), ['code' => $code])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_code_is_blocked_after_too_many_attempts(): void
    {
        Notification::fake();
        $manager = $this->userWithRole('manager');
        $this->post(route('login'), ['email' => $manager->email, 'password' => 'password']);

        $code = null;
        Notification::assertSentTo($manager, TwoFactorCodeNotification::class, function ($n) use (&$code) {
            $code = $n->code;

            return true;
        });

        $wrong = $code === '123456' ? '654321' : '123456';
        foreach (range(1, 5) as $ignored) {
            $this->post(route('two-factor.verify'), ['code' => $wrong]);
        }

        $this->post(route('two-factor.verify'), ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_clients_and_experts_log_in_directly(): void
    {
        Notification::fake();
        $client = $this->userWithRole('client');

        $this->post(route('login'), ['email' => $client->email, 'password' => 'password'])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($client);
        Notification::assertNothingSent();
    }
}
