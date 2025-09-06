<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
// Add reviewed_at if old typo exists or column missing
            if (Schema::hasColumn('certificate_requests', 'revieweded_at')) {
                $table->renameColumn('revieweded_at', 'reviewed_at');
            } elseif (! Schema::hasColumn('certificate_requests', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }


// Ensure a student can only have one request per subject
            if (! $this->hasIndex('certificate_requests', 'certificate_requests_student_subject_unique')) {
                $table->unique(['student_id','subject_id'], 'certificate_requests_student_subject_unique');
            }


// Helpful quick filter
            if (! $this->hasIndex('certificate_requests', 'certificate_requests_status_index')) {
                $table->index('status', 'certificate_requests_status_index');
            }
        });
    }


    public function down(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            if ($this->hasIndex('certificate_requests', 'certificate_requests_student_subject_unique')) {
                $table->dropUnique('certificate_requests_student_subject_unique');
            }
            if ($this->hasIndex('certificate_requests', 'certificate_requests_status_index')) {
                $table->dropIndex('certificate_requests_status_index');
            }
            if (Schema::hasColumn('certificate_requests', 'reviewed_at') && ! Schema::hasColumn('certificate_requests', 'revieweded_at')) {
// Do not recreate the typo on down; leave as-is for safety.
                $table->dropColumn('reviewed_at');
            }
        });
    }


    private function hasIndex(string $table, string $index): bool
    {
// Laravel doesn't expose an easy cross-DB way; be defensive.
// This returns false in SQLite, which is fine — migrations remain idempotent.
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails($table);
            return $doctrineTable->hasIndex($index);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
