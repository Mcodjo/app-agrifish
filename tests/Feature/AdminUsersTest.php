<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_only_admins_can_manage_users(): void
    {
        $this->actingAs($this->userWithRole('expert'))->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($this->userWithRole('client'))->post(route('admin.users.store'), [])->assertForbidden();
        $this->actingAs($this->userWithRole('admin'))->get(route('admin.users.index'))->assertOk();
    }

    public function test_admin_creates_an_expert_who_receives_an_invitation(): void
    {
        Notification::fake();

        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Kouassi Yao',
            'email' => 'kouassi@example.com',
            'role' => 'expert',
        ])->assertRedirect(route('admin.users.index'));

        $expert = User::where('email', 'kouassi@example.com')->firstOrFail();

        $this->assertSame('expert', $expert->role);
        Notification::assertSentTo($expert, AccountInvitation::class);
    }

    public function test_admin_cannot_demote_or_suspend_themselves(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->patch(route('admin.users.update', $admin), ['role' => 'client'])->assertSessionHasErrors('role');
        $this->actingAs($admin)->post(route('admin.users.suspension', $admin))->assertSessionHasErrors('suspension');

        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertFalse($admin->fresh()->isSuspended());
    }

    public function test_expert_with_open_projects_cannot_be_suspended(): void
    {
        $admin = $this->userWithRole('admin');
        $expert = $this->userWithRole('expert');

        ServiceRequest::create([
            'reference' => ServiceRequest::generateReference(),
            'client_id' => $this->userWithRole('client')->id,
            'assigned_expert_id' => $expert->id,
            'service_type' => 'agriculture',
            'title' => 'Projet',
            'description' => 'Description suffisamment longue.',
            'priority' => 'normal',
            'status' => 'in_progress',
        ]);

        $this->actingAs($admin)->post(route('admin.users.suspension', $expert))->assertSessionHasErrors('suspension');
        $this->assertFalse($expert->fresh()->isSuspended());
    }

    public function test_suspended_user_is_logged_out_and_cannot_log_in(): void
    {
        $admin = $this->userWithRole('admin');
        $client = $this->userWithRole('client');

        $this->actingAs($admin)->post(route('admin.users.suspension', $client))->assertSessionHasNoErrors();
        $this->assertTrue($client->fresh()->isSuspended());

        $this->actingAs($client->fresh())->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login'), ['email' => $client->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($admin)->post(route('admin.users.suspension', $client));
        $this->assertFalse($client->fresh()->isSuspended());
    }

    public function test_password_reset_form_does_not_reveal_unknown_addresses(): void
    {
        $this->post(route('password.email'), ['email' => 'inconnu@example.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');
    }
}
