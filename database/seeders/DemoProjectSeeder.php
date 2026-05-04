<?php

namespace Database\Seeders;

use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoProjectSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::query()->where('email', 'client@agrifish.africa')->firstOrFail();
        $expert = User::query()->where('email', 'expert@agrifish.africa')->firstOrFail();

        $demoFiles = [
            'demo-seed/request-brief.pdf' => 'Demande de cadrage AgriFish - document de test',
            'demo-seed/project-report.pdf' => 'Rapport technique de test AgriFish',

            'demo-seed/contract-draft.docx' => 'Brouillon de contrat de test AgriFish',
            'demo-seed/deliverable-plan.xlsx' => 'Tableau de jalons de test AgriFish',
            'demo-seed/deliverable-report.pdf' => 'Livrable de test AgriFish',
            'demo-seed/client-note.txt' => 'Note interne de test',
        ];

        foreach ($demoFiles as $path => $content) {
            Storage::disk('public')->put($path, $content);
        }

        $firstRequest = ServiceRequest::updateOrCreate(
            ['reference' => 'SRQ-DEMO-1001'],
            [
                'client_id' => $client->id,
                'service_type' => 'agriculture',
                'title' => 'Diagnostic parcellaire et plan de culture',
                'description' => 'Demande de démonstration pour tester le suivi projet, la messagerie et le téléchargement de livrables.',
                'priority' => 'high',
                'status' => 'in_progress',
                'assigned_expert_id' => $expert->id,
                'document_paths' => [
                    'demo-seed/request-brief.pdf',
                    'demo-seed/project-report.pdf',
                    'demo-seed/contract-draft.docx',
                ],
                'status_notes' => 'Projet de test prêt pour la validation des jalons.',
                'status_changed_at' => now(),
            ]
        );

        $secondRequest = ServiceRequest::updateOrCreate(
            ['reference' => 'SRQ-DEMO-1002'],
            [
                'client_id' => $client->id,
                'service_type' => 'aquaculture',
                'title' => 'Mise en place d’un suivi piscicole',
                'description' => 'Deuxième demande de démonstration pour tester plusieurs statuts et différents fichiers.',
                'priority' => 'normal',
                'status' => 'reviewing',
                'assigned_expert_id' => $expert->id,
                'document_paths' => [
                    'demo-seed/request-brief.pdf',
                ],
                'status_notes' => 'En cours de revue technique.',
                'status_changed_at' => now(),
            ]
        );

        foreach ([
            [
                'service_request_id' => $firstRequest->id,
                'title' => 'Cadrage initial',
                'description' => 'Analyse du besoin, collecte des contraintes et planification.',
                'status' => 'validated',
                'due_date' => now()->addDays(5)->toDateString(),
                'sort_order' => 1,
                'deliverable_paths' => ['demo-seed/deliverable-plan.xlsx'],
            ],
            [
                'service_request_id' => $firstRequest->id,
                'title' => 'Visite terrain',
                'description' => 'Inspection et collecte des données terrain.',
                'status' => 'in_progress',
                'due_date' => now()->addDays(10)->toDateString(),
                'sort_order' => 2,
                'deliverable_paths' => ['demo-seed/deliverable-report.pdf'],
            ],
            [
                'service_request_id' => $secondRequest->id,
                'title' => 'Revue documentaire',
                'description' => 'Lecture des documents et préparation du retour expert.',
                'status' => 'blocked',
                'due_date' => now()->addDays(3)->toDateString(),
                'sort_order' => 1,
                'deliverable_paths' => [],
            ],
        ] as $milestoneData) {
            ProjectMilestone::updateOrCreate(
                [
                    'service_request_id' => $milestoneData['service_request_id'],
                    'title' => $milestoneData['title'],
                ],
                $milestoneData
            );
        }

        foreach ([
            [
                'service_request_id' => $firstRequest->id,
                'sender_id' => $client->id,
                'body' => 'Bonjour, je teste la messagerie interne avec la demande de démonstration.',
                'attachment_path' => 'demo-seed/client-note.txt',
            ],
            [
                'service_request_id' => $firstRequest->id,
                'sender_id' => $expert->id,
                'body' => 'Bonjour, le premier jalon est prêt. Vous pouvez consulter le livrable ci-joint.',
                'attachment_path' => 'demo-seed/deliverable-report.pdf',
            ],
        ] as $messageData) {
            ProjectMessage::updateOrCreate(
                [
                    'service_request_id' => $messageData['service_request_id'],
                    'sender_id' => $messageData['sender_id'],
                    'body' => $messageData['body'],
                ],
                $messageData
            );
        }
    }
}
