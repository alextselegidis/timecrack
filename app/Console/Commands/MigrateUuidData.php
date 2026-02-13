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

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MigrateUuidData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-uuid-data {--source-db= : The source database connection with UUID data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from UUID-based tables to integer ID tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourceDb = $this->option('source-db') ?? 'mysql_old';

        $this->info('Starting UUID to Integer ID migration...');
        $this->info("Source database connection: {$sourceDb}");

        if (!config("database.connections.{$sourceDb}")) {
            $this->error("Source database connection '{$sourceDb}' not configured.");
            $this->info("Add a connection in config/database.php pointing to your old UUID database.");
            return Command::FAILURE;
        }

        try {
            DB::beginTransaction();

            // Migrate users
            $this->migrateUsers($sourceDb);

            // Migrate projects
            $this->migrateProjects($sourceDb);

            // Migrate project_user pivot
            $this->migrateProjectUser($sourceDb);

            // Migrate tasks to trackings
            $this->migrateTasksToTrackings($sourceDb);

            // Migrate settings
            $this->migrateSettings($sourceDb);

            DB::commit();

            $this->info('Migration completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function migrateUsers(string $sourceDb): void
    {
        $this->info('Migrating users...');

        $oldUsers = DB::connection($sourceDb)->table('users')->get();
        $uuidToIdMap = [];

        foreach ($oldUsers as $oldUser) {
            $name = trim(($oldUser->first_name ?? '') . ' ' . ($oldUser->last_name ?? ''));
            if (empty($name)) {
                $name = explode('@', $oldUser->email)[0];
            }

            $newId = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => $oldUser->email,
                'email_verified_at' => $oldUser->email_verified_at ?? null,
                'password' => $oldUser->password,
                'is_active' => true,
                'role' => $oldUser->role ?? 'user',
                'created_at' => $oldUser->created_at,
                'updated_at' => $oldUser->updated_at,
            ]);

            $uuidToIdMap[$oldUser->id] = $newId;
        }

        $this->userUuidMap = $uuidToIdMap;
        $this->info("  Migrated " . count($oldUsers) . " users.");
    }

    private function migrateProjects(string $sourceDb): void
    {
        $this->info('Migrating projects...');

        $oldProjects = DB::connection($sourceDb)->table('projects')->get();
        $uuidToIdMap = [];

        foreach ($oldProjects as $oldProject) {
            $newId = DB::table('projects')->insertGetId([
                'name' => $oldProject->name,
                'description' => $oldProject->description,
                'color' => null,
                'created_at' => $oldProject->created_at,
                'updated_at' => $oldProject->updated_at,
            ]);

            $uuidToIdMap[$oldProject->id] = $newId;
        }

        $this->projectUuidMap = $uuidToIdMap;
        $this->info("  Migrated " . count($oldProjects) . " projects.");
    }

    private function migrateProjectUser(string $sourceDb): void
    {
        $this->info('Migrating project_user relationships...');

        if (!Schema::connection($sourceDb)->hasTable('project_user')) {
            $this->info('  No project_user table found, skipping.');
            return;
        }

        $oldPivots = DB::connection($sourceDb)->table('project_user')->get();
        $count = 0;

        foreach ($oldPivots as $pivot) {
            $projectId = $this->projectUuidMap[$pivot->project_id] ?? null;
            $userId = $this->userUuidMap[$pivot->user_id] ?? null;

            if ($projectId && $userId) {
                DB::table('project_user')->insert([
                    'project_id' => $projectId,
                    'user_id' => $userId,
                ]);
                $count++;
            }
        }

        $this->info("  Migrated {$count} project_user relationships.");
    }

    private function migrateTasksToTrackings(string $sourceDb): void
    {
        $this->info('Migrating tasks to trackings...');

        $oldTasks = DB::connection($sourceDb)->table('tasks')->get();
        $count = 0;

        foreach ($oldTasks as $task) {
            $projectId = $this->projectUuidMap[$task->project_id] ?? null;
            $userId = $this->userUuidMap[$task->user_id] ?? null;

            if ($projectId && $userId && $task->started_at && $task->ended_at) {
                DB::table('trackings')->insert([
                    'project_id' => $projectId,
                    'user_id' => $userId,
                    'started_at' => $task->started_at,
                    'ended_at' => $task->ended_at,
                    'paused_duration' => 0,
                    'message' => $task->summary,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ]);
                $count++;
            }
        }

        $this->info("  Migrated {$count} tasks to trackings.");
    }

    private function migrateSettings(string $sourceDb): void
    {
        $this->info('Migrating settings...');

        if (!Schema::connection($sourceDb)->hasTable('settings')) {
            $this->info('  No settings table found, skipping.');
            return;
        }

        $oldSettings = DB::connection($sourceDb)->table('settings')->get();
        $count = 0;

        foreach ($oldSettings as $setting) {
            DB::table('settings')->insert([
                'name' => $setting->name,
                'value' => $setting->value,
                'created_at' => $setting->created_at ?? now(),
                'updated_at' => $setting->updated_at ?? now(),
            ]);
            $count++;
        }

        $this->info("  Migrated {$count} settings.");
    }

    private array $userUuidMap = [];
    private array $projectUuidMap = [];
}
