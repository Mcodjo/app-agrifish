<?php

namespace Tests\Feature;

use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\ServiceRequestStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function makeRequest(User $client, ?User $expert = null, array $attributes = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'reference' => ServiceRequest::generateReference(),
            'client_id' => $client->id,
            'service_type' => 'agriculture',
            'title' => 'Projet test',
            'description' => 'Description suffisamment longue pour le test.',
            'priority' => 'normal',
            'status' => 'new',
            'assigned_expert_id' => $expert?->id,
        ], $attributes));
    }

    public function test_client_cannot_view_another_clients_project(): void
    {
        $owner = $this->userWithRole('client');
        $other = $this->userWithRole('client');
        $project = $this->makeRequest($owner);

        $this->actingAs($other)->get(route('projects.show', $project))->assertForbidden();
        $this->actingAs($owner)->get(route('projects.show', $project))->assertOk();
    }

    public function test_expert_only_sees_assigned_projects(): void
    {
        $client = $this->userWithRole('client');
        $assigned = $this->userWithRole('expert');
        $otherExpert = $this->userWithRole('expert');
        $project = $this->makeRequest($client, $assigned);

        $this->actingAs($assigned)->get(route('projects.show', $project))->assertOk();
        $this->actingAs($otherExpert)->get(route('projects.show', $project))->assertForbidden();
        $this->actingAs($otherExpert)
            ->post(route('projects.messages.store', $project), ['body' => 'Bonjour'])
            ->assertForbidden();
    }

    public function test_only_assigned_expert_or_admin_can_manage_milestones(): void
    {
        $client = $this->userWithRole('client');
        $assigned = $this->userWithRole('expert');
        $otherExpert = $this->userWithRole('expert');
        $admin = $this->userWithRole('admin');
        $project = $this->makeRequest($client, $assigned);
        $payload = ['title' => 'Jalon', 'status' => 'planned'];

        $this->actingAs($client)->post(route('projects.milestones.store', $project), $payload)->assertForbidden();
        $this->actingAs($otherExpert)->post(route('projects.milestones.store', $project), $payload)->assertForbidden();
        $this->actingAs($assigned)->post(route('projects.milestones.store', $project), $payload)->assertRedirect();
        $this->actingAs($admin)->post(route('projects.milestones.store', $project), $payload)->assertRedirect();

        $this->assertSame(2, $project->milestones()->count());
    }

    public function test_uploaded_documents_are_private_and_downloadable_only_by_authorized_users(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $client = $this->userWithRole('client');
        $intruder = $this->userWithRole('client');

        $this->actingAs($client)->post(route('requests.store'), [
            'service_type' => 'agriculture',
            'title' => 'Demande avec document',
            'description' => 'Une description de plus de vingt caractères.',
            'priority' => 'normal',
            'documents' => [UploadedFile::fake()->create('etude.pdf', 10, 'application/pdf')],
        ])->assertRedirect(route('dashboard'));

        $project = ServiceRequest::firstOrFail();
        $path = $project->document_paths[0];

        Storage::disk('local')->assertExists($path);
        Storage::disk('public')->assertMissing($path);

        $this->actingAs($client)->get(route('projects.documents.download', [$project, 0]))->assertOk();
        $this->actingAs($intruder)->get(route('projects.documents.download', [$project, 0]))->assertForbidden();
    }

    public function test_client_can_download_milestone_deliverables_and_message_attachments(): void
    {
        Storage::fake('local');

        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');
        $project = $this->makeRequest($client, $expert);

        Storage::disk('local')->put('project-deliverables/1/rapport.pdf', 'contenu');
        Storage::disk('local')->put('project-messages/1/note.pdf', 'contenu');

        $milestone = ProjectMilestone::create([
            'service_request_id' => $project->id,
            'title' => 'Livraison',
            'status' => 'completed',
            'sort_order' => 1,
            'deliverable_paths' => ['project-deliverables/1/rapport.pdf'],
        ]);

        $message = ProjectMessage::create([
            'service_request_id' => $project->id,
            'sender_id' => $expert->id,
            'body' => 'Voici le rapport',
            'attachment_path' => 'project-messages/1/note.pdf',
        ]);

        $this->actingAs($client)->get(route('projects.milestones.download', [$milestone, 0]))->assertOk();
        $this->actingAs($client)->get(route('projects.messages.attachment', [$project, $message]))->assertOk();

        // Une pièce jointe ne peut pas être récupérée via le projet d'un autre client.
        $otherClient = $this->userWithRole('client');
        $otherProject = $this->makeRequest($otherClient);
        $this->actingAs($otherClient)
            ->get(route('projects.messages.attachment', [$otherProject, $message]))
            ->assertNotFound();
    }

    public function test_non_admin_cannot_access_admin_area(): void
    {
        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');

        $this->actingAs($client)->get(route('admin.requests.index'))->assertForbidden();
        $this->actingAs($expert)->get(route('admin.requests.index'))->assertForbidden();
        $this->actingAs($this->userWithRole('admin'))->get(route('admin.requests.index'))->assertOk();
    }

    public function test_admin_can_only_assign_users_with_expert_role(): void
    {
        Notification::fake();

        $admin = $this->userWithRole('admin');
        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');
        $project = $this->makeRequest($client);

        $this->actingAs($admin)
            ->patch(route('admin.requests.update', $project), ['status' => 'assigned', 'assigned_expert_id' => $client->id])
            ->assertSessionHasErrors('assigned_expert_id');

        $this->actingAs($admin)
            ->patch(route('admin.requests.update', $project), ['status' => 'assigned', 'assigned_expert_id' => $expert->id])
            ->assertSessionHasNoErrors();

        $this->assertSame($expert->id, $project->fresh()->assigned_expert_id);
        Notification::assertSentTo($client, ServiceRequestStatusChanged::class);
    }

    public function test_client_is_not_notified_when_status_is_unchanged_or_notifications_disabled(): void
    {
        Notification::fake();

        $admin = $this->userWithRole('admin');
        $client = $this->userWithRole('client');
        $client->forceFill(['preferences' => ['status_notifications' => false]])->save();
        $project = $this->makeRequest($client);

        $this->actingAs($admin)->patch(route('admin.requests.update', $project), ['status' => 'new']);
        $this->actingAs($admin)->patch(route('admin.requests.update', $project), ['status' => 'reviewing']);

        Notification::assertNothingSent();
    }

    public function test_role_cannot_be_mass_assigned(): void
    {
        $user = $this->userWithRole('client');

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Pirate',
            'email' => $user->email,
            'role' => 'admin',
        ]);

        $this->assertSame('client', $user->fresh()->role);

        $created = User::create([
            'name' => 'X', 'email' => 'x@example.com', 'password' => 'secret123', 'role' => 'admin',
        ]);
        $this->assertSame('client', $created->fresh()->role);
    }

    public function test_service_type_must_be_a_known_value(): void
    {
        $client = $this->userWithRole('client');

        $this->actingAs($client)->post(route('requests.store'), [
            'service_type' => '<script>alert(1)</script>',
            'title' => 'Titre',
            'description' => 'Une description de plus de vingt caractères.',
            'priority' => 'normal',
        ])->assertSessionHasErrors('service_type');
    }

    public function test_expert_dashboard_lists_assigned_projects(): void
    {
        $client = $this->userWithRole('client');
        $expert = $this->userWithRole('expert');
        $project = $this->makeRequest($client, $expert, ['title' => 'Projet assigné visible']);

        $this->actingAs($expert)->get(route('dashboard'))->assertOk()->assertSee($project->reference);
    }

    public function test_security_headers_are_present(): void
    {
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
