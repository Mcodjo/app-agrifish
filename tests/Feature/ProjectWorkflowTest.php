<?php

namespace Tests\Feature;

use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\NewProjectMessage;
use App\Notifications\ProjectAssigned;
use App\Notifications\ServiceRequestActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function makeRequest(User $client, ?User $expert = null, string $status = 'new'): ServiceRequest
    {
        return ServiceRequest::create([
            'reference' => ServiceRequest::generateReference(),
            'client_id' => $client->id,
            'service_type' => 'aquaculture',
            'title' => 'Étangs piscicoles',
            'description' => 'Description suffisamment longue pour le test.',
            'priority' => 'normal',
            'status' => $status,
            'assigned_expert_id' => $expert?->id,
        ]);
    }

    public function test_client_message_notifies_the_assigned_expert_only(): void
    {
        Notification::fake();

        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');
        $admin = $this->userWithRole('admin');
        $project = $this->makeRequest($client, $expert);

        $this->actingAs($client)
            ->post(route('projects.messages.store', $project), ['body' => 'Voici le plan de la parcelle.'])
            ->assertRedirect();

        Notification::assertSentTo($expert, NewProjectMessage::class);
        Notification::assertNotSentTo($client, NewProjectMessage::class);
        Notification::assertNotSentTo($admin, NewProjectMessage::class);
    }

    public function test_message_without_expert_goes_to_admins_and_reply_goes_to_client(): void
    {
        Notification::fake();

        $client = $this->userWithRole('client');
        $admin = $this->userWithRole('admin');
        $project = $this->makeRequest($client);

        $this->actingAs($client)->post(route('projects.messages.store', $project), ['body' => 'Bonjour']);
        Notification::assertSentTo($admin, NewProjectMessage::class);

        $this->actingAs($admin)->post(route('projects.messages.store', $project), ['body' => 'Bien reçu']);
        Notification::assertSentTo($client, NewProjectMessage::class);
    }

    public function test_message_notifications_respect_preferences(): void
    {
        Notification::fake();

        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert', ['preferences' => ['message_notifications' => false]]);
        $project = $this->makeRequest($client, $expert);

        $this->actingAs($client)->post(route('projects.messages.store', $project), ['body' => 'Bonjour']);

        Notification::assertNothingSent();
    }

    public function test_new_request_notifies_admins_and_is_recorded(): void
    {
        Notification::fake();

        $admin = $this->userWithRole('admin');
        $client = $this->userWithRole('client');

        $this->actingAs($client)->post(route('requests.store'), [
            'service_type' => 'agriculture',
            'title' => 'Plan de culture',
            'description' => 'Une description de plus de vingt caractères.',
            'priority' => 'high',
        ]);

        $project = ServiceRequest::firstOrFail();

        Notification::assertSentTo($admin, ServiceRequestActivity::class);
        $this->assertSame(['created'], $project->events()->pluck('type')->all());
    }

    public function test_assigned_expert_is_notified_and_history_is_recorded(): void
    {
        Notification::fake();

        $admin = $this->userWithRole('admin');
        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');
        $project = $this->makeRequest($client);

        $this->actingAs($admin)->patch(route('admin.requests.update', $project), [
            'status' => 'assigned',
            'assigned_expert_id' => $expert->id,
        ])->assertSessionHasNoErrors();

        Notification::assertSentTo($expert, ProjectAssigned::class);
        $this->assertEqualsCanonicalizing(['status', 'assigned'], $project->events()->pluck('type')->all());

        // Même expert, même statut : rien de nouveau.
        $this->actingAs($admin)->patch(route('admin.requests.update', $project), [
            'status' => 'assigned',
            'assigned_expert_id' => $expert->id,
        ]);

        Notification::assertSentToTimes($expert, ProjectAssigned::class, 1);
        $this->assertSame(2, $project->events()->count());

        $this->actingAs($client)->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Expert assigné : '.$expert->name);
    }

    public function test_client_can_cancel_only_before_work_starts(): void
    {
        Notification::fake();

        $client = $this->userWithRole('client');
        $admin = $this->userWithRole('admin');
        $other = $this->userWithRole('client');
        $project = $this->makeRequest($client);

        $this->actingAs($other)->post(route('projects.cancel', $project))->assertForbidden();

        $this->actingAs($client)
            ->post(route('projects.cancel', $project), ['reason' => 'Projet reporté'])
            ->assertRedirect(route('projects.show', $project));

        $this->assertSame('cancelled', $project->fresh()->status);
        Notification::assertSentTo($admin, ServiceRequestActivity::class);

        // Messagerie fermée après annulation.
        $this->actingAs($client)->post(route('projects.messages.store', $project), ['body' => 'Encore là ?'])->assertForbidden();

        $started = $this->makeRequest($client, null, 'in_progress');
        $this->actingAs($client)->post(route('projects.cancel', $started))->assertForbidden();
    }

    public function test_client_can_add_and_remove_documents_while_request_is_new(): void
    {
        Storage::fake('local');

        $client = $this->userWithRole('client');
        $project = $this->makeRequest($client);

        $this->actingAs($client)->post(route('projects.documents.store', $project), [
            'documents' => [UploadedFile::fake()->create('plan.pdf', 10, 'application/pdf')],
        ])->assertSessionHasNoErrors();

        $path = $project->fresh()->document_paths[0];
        Storage::disk('local')->assertExists($path);

        $this->actingAs($client)->delete(route('projects.documents.destroy', [$project, 0]))->assertRedirect();

        $this->assertSame([], $project->fresh()->document_paths);
        Storage::disk('local')->assertMissing($path);

        $project->update(['status' => 'in_progress', 'document_paths' => [$path]]);
        $this->actingAs($client)->delete(route('projects.documents.destroy', [$project, 0]))->assertForbidden();
    }

    public function test_expert_can_reorder_and_delete_milestones_and_deliverables(): void
    {
        Storage::fake('local');

        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');
        $project = $this->makeRequest($client, $expert, 'in_progress');

        $first = ProjectMilestone::create(['service_request_id' => $project->id, 'title' => 'Diagnostic', 'status' => 'completed', 'sort_order' => 1]);
        $second = ProjectMilestone::create(['service_request_id' => $project->id, 'title' => 'Plan', 'status' => 'planned', 'sort_order' => 2]);

        $this->actingAs($client)->post(route('projects.milestones.move', $second), ['direction' => 'up'])->assertForbidden();

        $this->actingAs($expert)->post(route('projects.milestones.move', $second), ['direction' => 'up'])->assertRedirect();
        $this->assertSame(['Plan', 'Diagnostic'], $project->milestones()->orderBy('sort_order')->pluck('title')->all());

        Storage::disk('local')->put('project-deliverables/'.$first->id.'/rapport.pdf', 'contenu');
        $first->update(['deliverable_paths' => ['project-deliverables/'.$first->id.'/rapport.pdf']]);

        $this->actingAs($expert)->delete(route('projects.milestones.deliverables.destroy', [$first, 0]))->assertRedirect();
        $this->assertSame([], $first->fresh()->deliverable_paths);
        Storage::disk('local')->assertMissing('project-deliverables/'.$first->id.'/rapport.pdf');

        $this->actingAs($expert)->delete(route('projects.milestones.destroy', $second))->assertRedirect();
        $this->assertSame(['Diagnostic'], $project->milestones()->pluck('title')->all());
    }

    public function test_dashboard_filters_and_search(): void
    {
        $client = $this->userWithRole('client');
        $open = $this->makeRequest($client, null, 'in_progress');
        $done = $this->makeRequest($client, null, 'completed');
        $done->update(['title' => 'Formation pisciculture']);

        $this->actingAs($client)->get(route('dashboard', ['filtre' => 'terminees']))
            ->assertOk()->assertSee($done->reference)->assertDontSee($open->reference);

        $this->actingAs($client)->get(route('dashboard', ['q' => 'pisciculture']))
            ->assertOk()->assertSee($done->reference)->assertDontSee($open->reference);
    }

    public function test_admin_dashboard_shows_indicators(): void
    {
        $admin = $this->userWithRole('admin');
        $expert = $this->userWithRole('expert', ['name' => 'Awa Diallo']);
        $this->makeRequest($this->userWithRole('client'), $expert, 'in_progress');

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Charge des experts')
            ->assertSee('Awa Diallo')
            ->assertSee('Demandes ce mois-ci');
    }
}
