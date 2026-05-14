<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure idempotency for all index creations
        $this->safeIndex('job_applications', 'job_id', 'job_applications_job_id_index');
        $this->safeIndex('job_applications', 'worker_id', 'job_applications_worker_id_index');
        $this->safeIndex('job_applications', ['status', 'created_at'], 'job_applications_status_created_at_index');
        $this->safeIndex('job_postings', ['status', 'published_at'], 'job_postings_status_published_at_index');
        $this->safeIndex('job_postings', 'employer_id', 'job_postings_employer_id_index');
        $this->safeIndex('job_postings', 'expires_at', 'job_postings_expires_at_index');
        $this->safeIndex('users', 'role', 'users_role_index');
        $this->safeIndex('audit_logs', ['user_id', 'created_at'], 'audit_logs_user_id_created_at_index');
        $this->safeIndex('audit_logs', 'action', 'audit_logs_action_index');
        $this->safeIndex('notifications', ['notifiable_id', 'read_at'], 'notifications_notifiable_id_read_at_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ensure idempotency for all index deletions
        $this->safeDropIndex('job_applications', 'job_applications_job_id_index');
        $this->safeDropIndex('job_applications', 'job_applications_worker_id_index');
        $this->safeDropIndex('job_applications', 'job_applications_status_created_at_index');
        $this->safeDropIndex('job_postings', 'job_postings_status_published_at_index');
        $this->safeDropIndex('job_postings', 'job_postings_employer_id_index');
        $this->safeDropIndex('job_postings', 'job_postings_expires_at_index');
        $this->safeDropIndex('users', 'users_role_index');
        $this->safeDropIndex('audit_logs', 'audit_logs_user_id_created_at_index');
        $this->safeDropIndex('audit_logs', 'audit_logs_action_index');
        $this->safeDropIndex('notifications', 'notifications_notifiable_id_read_at_index');
    }

    /**
     * Check if index exists on table (helper for safe migration)
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                $query = "PRAGMA index_list('$table')";
                $indexes = DB::select($query);
                foreach ($indexes as $index) {
                    if (isset($index->name) && $index->name === $indexName) {
                        return true;
                    }
                }
                return false;
            } elseif (in_array($driver, ['mysql', 'mariadb'])) {
                $query = "SHOW INDEX FROM `" . str_replace('`', '``', $table) . "` WHERE Key_name = ?";
                $indexes = DB::select($query, [$indexName]);
                return !empty($indexes);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if column exists on table (helper for safe migration)
     */
    private function columnExists(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Safely add an index to a table (helper for safe migration)
     */
    private function safeIndex(string $table, $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = (array) $columns;
        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return;
            }
        }

        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        }
    }

    /**
     * Safely drop an index from a table (helper for safe migration)
     */
    private function safeDropIndex(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
