<?php

namespace Database\Seeders;

use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Support\PrivateFiles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoProjectSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::query()->where('email', 'client@agrifish.africa')->firstOrFail();
        $expert = User::query()->where('email', 'expert@agrifish.africa')->firstOrFail();

        $demoFiles = [
            'demo-seed/request-brief.pdf' => 'Plan des parcelles (exemple)',
            'demo-seed/project-report.pdf' => 'Résultats de la campagne précédente (exemple)',

            'demo-seed/contract-draft.docx' => 'Proposition d’intervention (exemple)',
            'demo-seed/deliverable-plan.xlsx' => 'Planning de la mission (exemple)',
            'demo-seed/deliverable-report.pdf' => 'Compte rendu de visite (exemple)',
            'demo-seed/client-note.txt' => 'Note du client (exemple)',
        ];

        foreach ($demoFiles as $path => $content) {
            Storage::disk(PrivateFiles::DISK)->put($path, $content);
        }

        $firstRequest = ServiceRequest::updateOrCreate(
            ['reference' => 'SRQ-K7M2Q4TB'],
            [
                'client_id' => $client->id,
                'service_type' => 'agriculture',
                'title' => 'Diagnostic parcellaire et plan de culture',
                'description' => 'Exploitation maraîchère de 4 hectares. Nous souhaitons un diagnostic des parcelles et un plan de culture pour la saison sèche, avec un calendrier d’irrigation.',
                'priority' => 'high',
                'status' => 'in_progress',
                'assigned_expert_id' => $expert->id,
                'document_paths' => [
                    'demo-seed/request-brief.pdf',
                    'demo-seed/project-report.pdf',
                    'demo-seed/contract-draft.docx',
                ],
                'status_notes' => 'Visite de terrain réalisée. Le compte rendu est disponible dans les livrables de la première étape.',
                'status_changed_at' => now(),
            ]
        );

        $secondRequest = ServiceRequest::updateOrCreate(
            ['reference' => 'SRQ-P3X8N6VD'],
            [
                'client_id' => $client->id,
                'service_type' => 'aquaculture',
                'title' => 'Mise en place d’un suivi piscicole',
                'description' => 'Deux étangs de 400 m² en production de tilapia. Nous voulons mettre en place un suivi de la qualité de l’eau et de l’alimentation.',
                'priority' => 'normal',
                'status' => 'reviewing',
                'assigned_expert_id' => $expert->id,
                'document_paths' => [
                    'demo-seed/request-brief.pdf',
                ],
                'status_notes' => 'Demande en cours d’analyse par notre équipe.',
                'status_changed_at' => now(),
            ]
        );

        foreach ([
            [
                'service_request_id' => $firstRequest->id,
                'title' => 'Cadrage initial',
                'description' => 'Entretien, collecte des documents existants et préparation de la visite.',
                'status' => 'validated',
                'due_date' => now()->addDays(5)->toDateString(),
                'sort_order' => 1,
                'deliverable_paths' => ['demo-seed/deliverable-plan.xlsx'],
            ],
            [
                'service_request_id' => $firstRequest->id,
                'title' => 'Visite terrain',
                'description' => 'Visite des parcelles, prélèvements de sol et relevé du système d’irrigation.',
                'status' => 'in_progress',
                'due_date' => now()->addDays(10)->toDateString(),
                'sort_order' => 2,
                'deliverable_paths' => ['demo-seed/deliverable-report.pdf'],
            ],
            [
                'service_request_id' => $firstRequest->id,
                'title' => 'Plan de culture',
                'description' => 'Choix des cultures, calendrier et besoins en eau pour la saison sèche.',
                'status' => 'planned',
                'due_date' => now()->addDays(21)->toDateString(),
                'sort_order' => 3,
                'deliverable_paths' => [],
            ],
            [
                'service_request_id' => $secondRequest->id,
                'title' => 'Revue documentaire',
                'description' => 'En attente des relevés de température et d’oxygène des trois derniers mois.',
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
                'body' => 'Bonjour, je vous transmets le plan des parcelles et les résultats de la dernière campagne.',
                'attachment_path' => 'demo-seed/client-note.txt',
            ],
            [
                'service_request_id' => $firstRequest->id,
                'sender_id' => $expert->id,
                'body' => 'Merci. Le compte rendu de la visite est disponible dans la première étape. Je vous propose un échange jeudi pour en parler.',
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
    
        // Historique de démonstration, recréé à chaque exécution.
        $admin = User::where('role', 'admin')->first();

        foreach ([
            [$firstRequest, [
                ['created', $client, null, 'new', null, 12],
                ['status', $admin, 'new', 'reviewing', null, 11],
                ['assigned', $admin, null, null, $expert->name, 11],
                ['status', $admin, 'reviewing', 'in_progress', null, 9],
            ]],
            [$secondRequest, [
                ['created', $client, null, 'new', null, 2],
                ['status', $admin, 'new', 'reviewing', null, 1],
            ]],
        ] as [$serviceRequest, $events]) {
            $serviceRequest->events()->delete();

            $serviceRequest->forceFill(['created_at' => now()->subDays($events[0][5])->setTime(9, 30)])->save();

            foreach ($events as [$type, $user, $from, $to, $details, $daysAgo]) {
                $serviceRequest->events()->create([
                    'type' => $type,
                    'user_id' => $user?->id,
                    'from_status' => $from,
                    'to_status' => $to,
                    'details' => $details,
                ])->forceFill(['created_at' => now()->subDays($daysAgo)->setTime(9, 30)])->save();
            }
        }
}
}
