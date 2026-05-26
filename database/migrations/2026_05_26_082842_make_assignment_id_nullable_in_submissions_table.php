<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint first (if it exists), then make the column nullable.
        Schema::table('submissions', function (Blueprint $table) {
            // Drop the existing FK — Laravel names it submissions_assignment_id_foreign
            if ($this->foreignKeyExists('submissions', 'submissions_assignment_id_foreign')) {
                $table->dropForeign(['assignment_id']);
            }

            // Re-define the column as nullable
            $table->unsignedBigInteger('assignment_id')->nullable()->change();

            // Re-add the FK as nullable (onDelete set null so we don't orphan rows)
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('assignments')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->unsignedBigInteger('assignment_id')->nullable(false)->change();
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('assignments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Check if a named foreign key exists on a table.
     */
    private function foreignKeyExists(string $table, string $fkName): bool
    {
        $fks = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
              AND CONSTRAINT_NAME = ?
        ", [$table, $fkName]);

        return count($fks) > 0;
    }
};
