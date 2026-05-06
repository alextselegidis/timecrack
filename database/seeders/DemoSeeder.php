<?php

/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Project;
use App\Models\Tracking;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Demo seeder.
 *
 * Generates realistic demo data for the Timecrack application: one manager,
 * three IT developers, eight software projects and one full month of past
 * time-tracking entries with software-development related messages.
 *
 * IMPORTANT: This seeder is intentionally NOT registered in DatabaseSeeder
 * so that it never runs during normal `php artisan db:seed`. Run it
 * manually with:
 *
 *     php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    /**
     * The eight software projects the simulated team is working on.
     * Each entry contains a realistic name, a short description, a
     * tailwind-friendly hex color and the typical tasks that the team
     * performs on the project (used to generate realistic tracking
     * messages).
     *
     * @var array<int, array<string, mixed>>
     */
    private array $projects = [
        [
            'name' => 'Atlas CRM',
            'description' => 'Customer relationship management platform for the sales team.',
            'color' => '#2563eb',
            'tasks' => [
                'Implement contact deduplication logic',
                'Refactor lead scoring service',
                'Add pagination to the accounts list endpoint',
                'Fix N+1 query on the opportunities dashboard',
                'Wire up the activity timeline component',
                'Migrate legacy contact import job to queued worker',
            ],
        ],
        [
            'name' => 'Helios Billing',
            'description' => 'Subscription billing and invoicing micro-service.',
            'color' => '#f59e0b',
            'tasks' => [
                'Add proration support to subscription upgrades',
                'Integrate Stripe webhook for failed payments',
                'Generate PDF invoices with the new template',
                'Backfill missing tax rates on legacy invoices',
                'Cover the dunning workflow with feature tests',
                'Optimize monthly revenue report query',
            ],
        ],
        [
            'name' => 'Orion Mobile App',
            'description' => 'Cross-platform mobile client built with React Native.',
            'color' => '#10b981',
            'tasks' => [
                'Implement offline mode for the dashboard',
                'Fix push notification crash on Android 14',
                'Polish onboarding screens and animations',
                'Upgrade React Native to the latest stable',
                'Add biometric authentication on iOS',
                'Resolve memory leak in the chat screen',
            ],
        ],
        [
            'name' => 'Nimbus Analytics',
            'description' => 'Data warehousing and business intelligence dashboards.',
            'color' => '#8b5cf6',
            'tasks' => [
                'Build cohort retention chart component',
                'Tune slow Redshift query for the funnel report',
                'Schedule the nightly ETL pipeline in Airflow',
                'Add CSV export to the custom reports view',
                'Document the metrics dimensional model',
                'Backfill historical events into the new schema',
            ],
        ],
        [
            'name' => 'Pulse Internal Tools',
            'description' => 'Internal admin panel and operations tooling.',
            'color' => '#ef4444',
            'tasks' => [
                'Add role-based access control to admin routes',
                'Build feature flag toggling UI',
                'Automate user impersonation audit logging',
                'Migrate internal dashboard to Laravel 11',
                'Patch CSRF vulnerability reported by audit',
                'Add structured logging to the worker queue',
            ],
        ],
        [
            'name' => 'Vertex API Gateway',
            'description' => 'Public REST and GraphQL API gateway.',
            'color' => '#0ea5e9',
            'tasks' => [
                'Implement rate limiting per API token',
                'Write OpenAPI spec for the v2 endpoints',
                'Add circuit breaker for upstream services',
                'Improve error response payload consistency',
                'Set up canary deployment via Argo Rollouts',
                'Profile latency on the search endpoint',
            ],
        ],
        [
            'name' => 'Forge DevOps Platform',
            'description' => 'CI/CD pipelines, infrastructure as code and observability.',
            'color' => '#475569',
            'tasks' => [
                'Migrate Jenkins jobs to GitHub Actions',
                'Add Terraform module for the staging cluster',
                'Configure Grafana dashboards for SLOs',
                'Rotate production database credentials',
                'Harden Kubernetes network policies',
                'Reduce Docker image size for the API service',
            ],
        ],
        [
            'name' => 'Lumen Marketing Site',
            'description' => 'Public marketing website and CMS integration.',
            'color' => '#ec4899',
            'tasks' => [
                'Improve Lighthouse score on the landing page',
                'Wire up the new blog template to the CMS',
                'Add A/B test for the pricing page hero',
                'Fix layout shift on mobile navigation',
                'Set up sitemap generation in the build step',
                'Implement consent banner for analytics scripts',
            ],
        ],
    ];

    /**
     * The simulated team: one manager and three IT developers.
     *
     * @var array<int, array<string, string>>
     */
    private array $team = [
        ['name' => 'Sarah', 'email' => 'sarah@example.org', 'role' => 'manager'],
        ['name' => 'David', 'email' => 'david@example.org', 'role' => 'developer'],
        ['name' => 'Priya', 'email' => 'priya@example.org', 'role' => 'developer'],
        ['name' => 'Lukas', 'email' => 'lukas@example.org', 'role' => 'developer'],
    ];

    /**
     * Seed the application's database with demo content.
     */
    public function run(): void
    {
        // Use a single transaction so a partial failure leaves no half-seeded
        // data behind.
        DB::transaction(function (): void {
            $users    = $this->seedTeam();
            $projects = $this->seedProjects();

            // Every team member needs to be assigned to every project so the
            // tracking screen offers them all as choices.
            $this->attachUsersToProjects($users, $projects);

            // Generate one full month of trackings (working weekdays only).
            $this->seedTrackings($users, $projects);
        });
    }

    /**
     * Create the manager and three developers.
     *
     * Uses updateOrCreate keyed on email so the seeder is idempotent and can
     * be re-run safely without producing duplicate users.
     *
     * @return array{manager: User, developers: array<int, User>}
     */
    private function seedTeam(): array
    {
        $manager    = null;
        $developers = [];

        foreach ($this->team as $member) {
            $isManager = $member['role'] === 'manager';

            $user = User::updateOrCreate(
                ['email' => $member['email']],
                [
                    'name'              => $member['name'],
                    'password'          => Hash::make('12345678'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    // Managers get the admin role so they can see everyone's
                    // trackings; developers stay on the regular user role.
                    'role'              => $isManager
                        ? RoleEnum::ADMIN->value
                        : RoleEnum::USER->value,
                ]
            );

            if ($isManager) {
                $manager = $user;
            } else {
                $developers[] = $user;
            }
        }

        return ['manager' => $manager, 'developers' => $developers];
    }

    /**
     * Create the eight software projects.
     *
     * Idempotent through firstOrCreate keyed on the project name.
     *
     * @return \Illuminate\Support\Collection<int, Project>
     */
    private function seedProjects()
    {
        return collect($this->projects)->map(function (array $data): Project {
            return Project::firstOrCreate(
                ['name' => $data['name']],
                [
                    'description' => $data['description'],
                    'color'       => $data['color'],
                ]
            );
        });
    }

    /**
     * Attach every team member to every project (many-to-many pivot).
     *
     * syncWithoutDetaching avoids duplicate pivot rows when re-seeding.
     */
    private function attachUsersToProjects(array $users, $projects): void
    {
        $projectIds = $projects->pluck('id')->all();

        $allUsers = array_merge([$users['manager']], $users['developers']);

        foreach ($allUsers as $user) {
            $user->projects()->syncWithoutDetaching($projectIds);
        }
    }

    /**
     * Generate one month worth of trackings for the team.
     *
     * Behaviour:
     *  - Only weekdays are simulated (Mon-Fri).
     *  - Developers fill ~6-8h per day across 1-3 projects.
     *  - The manager logs lighter, meeting-oriented trackings.
     *  - Work blocks never overlap for the same user on the same day.
     *  - billable_hours is set to the full block duration most of the time
     *    and slightly reduced for the rest (simulating breaks / non-billable
     *    work).
     */
    private function seedTrackings(array $users, $projects): void
    {
        // Wipe previously seeded demo trackings for these users so the seeder
        // can be re-run without piling up duplicates.
        $userIds = collect([$users['manager']])
            ->merge($users['developers'])
            ->pluck('id')
            ->all();

        Tracking::whereIn('user_id', $userIds)->delete();

        $today    = CarbonImmutable::today();
        $startDay = $today->subDays(30);

        // Iterate over each day in the past month.
        for ($day = $startDay; $day->lte($today); $day = $day->addDay()) {
            // Skip weekends - the team does not log time on Sat/Sun.
            if ($day->isWeekend()) {
                continue;
            }

            // Developers: full work day, 1-3 projects.
            foreach ($users['developers'] as $developer) {
                $this->seedDeveloperDay($developer, $day, $projects);
            }

            // Manager: lighter day, mostly oversight & coordination.
            $this->seedManagerDay($users['manager'], $day, $projects);
        }
    }

    /**
     * Seed a developer's working day with 2-4 sequential tracking blocks
     * spread between roughly 09:00 and 18:00.
     */
    private function seedDeveloperDay(User $developer, CarbonImmutable $day, $projects): void
    {
        // Skip a few random days to simulate days off / sick leave / PTO.
        if (random_int(1, 100) <= 10) {
            return;
        }

        $blockCount = random_int(2, 4);
        $cursor     = $day->setTime(9, random_int(0, 30));

        // Pick 1 to 3 projects the developer worked on this specific day.
        $dailyProjects = $projects->random(min($projects->count(), random_int(1, 3)));

        for ($i = 0; $i < $blockCount; $i++) {
            // Stop generating blocks if we are past the end of the work day.
            if ($cursor->hour >= 18) {
                break;
            }

            $project = $dailyProjects->random();

            // Block length: 45 to 150 minutes, in 15 minute increments.
            $minutes = random_int(3, 10) * 15;
            $endsAt  = $cursor->addMinutes($minutes);

            // Make sure we don't overflow into the evening.
            if ($endsAt->hour >= 19) {
                break;
            }

            $this->createTracking($developer, $project, $cursor, $endsAt);

            // Insert a 0-30 minute gap between work blocks (lunch / breaks).
            $gap    = random_int(0, 6) * 5;
            $cursor = $endsAt->addMinutes($gap);
        }
    }

    /**
     * Seed the manager's working day with a couple of shorter, coordination
     * focused trackings.
     */
    private function seedManagerDay(User $manager, CarbonImmutable $day, $projects): void
    {
        // Managers occasionally take focus / off-platform days.
        if (random_int(1, 100) <= 20) {
            return;
        }

        $blockCount = random_int(1, 3);
        $cursor     = $day->setTime(9, 30);

        $managerMessages = [
            'Sprint planning and backlog grooming',
            'One-on-one sync with the team',
            'Code review for the open pull requests',
            'Stakeholder status update meeting',
            'Roadmap refinement for next quarter',
            'Hiring loop debrief and candidate scoring',
            'Incident retrospective and follow-up actions',
            'Architecture review with the platform team',
        ];

        for ($i = 0; $i < $blockCount; $i++) {
            if ($cursor->hour >= 17) {
                break;
            }

            $project = $projects->random();
            $minutes = random_int(2, 6) * 15; // 30 - 90 minutes
            $endsAt  = $cursor->addMinutes($minutes);

            Tracking::create([
                'project_id'     => $project->id,
                'user_id'        => $manager->id,
                'started_at'     => $cursor,
                'ended_at'       => $endsAt,
                'billable_hours' => round(($endsAt->getTimestamp() - $cursor->getTimestamp()) / 3600, 2),
                'message'        => $managerMessages[array_rand($managerMessages)],
            ]);

            $cursor = $endsAt->addMinutes(random_int(0, 4) * 15);
        }
    }

    /**
     * Persist a single tracking row with a realistic developer message.
     */
    private function createTracking(User $user, Project $project, CarbonImmutable $startedAt, CarbonImmutable $endedAt): void
    {
        // Pick a project-specific task as the base message and occasionally
        // append a short clarifying note so messages are not identical.
        $projectMeta = collect($this->projects)->firstWhere('name', $project->name);
        $baseMessage = $projectMeta['tasks'][array_rand($projectMeta['tasks'])];

        $suffixes = [
            '',
            ' - paired with the team',
            ' (follow-up from yesterday)',
            ' - review feedback applied',
            ' - investigation phase',
            ' - PR opened for review',
        ];
        $message = $baseMessage . $suffixes[array_rand($suffixes)];

        $durationHours = ($endedAt->getTimestamp() - $startedAt->getTimestamp()) / 3600;

        // 80% of trackings are fully billable; the rest are slightly reduced
        // to simulate non-billable activities mixed into the block.
        $billableHours = random_int(1, 100) <= 80
            ? round($durationHours, 2)
            : round(max(0.25, $durationHours - 0.25 * random_int(1, 2)), 2);

        Tracking::create([
            'project_id'     => $project->id,
            'user_id'        => $user->id,
            'started_at'     => $startedAt,
            'ended_at'       => $endedAt,
            'billable_hours' => $billableHours,
            'message'        => $message,
        ]);
    }
}
