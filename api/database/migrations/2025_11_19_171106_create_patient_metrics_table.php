<?php

declare(strict_types=1);

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
        Schema::create('patient_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained('patients')->onDelete('cascade');
            $table->unsignedInteger('users_completed')->default(0);
            $table->unsignedInteger('users_attempted')->default(0);
            $table->unsignedInteger('users_attempted_not_completed')->default(0);
            $table->unsignedInteger('avg_step_time_sec')->default(0);
            $table->decimal('avg_correct_rate', 5, 4)->default(0); // 0.0000 a 1.0000
            $table->foreignId('hardest_step_id')->nullable()->constrained('steps')->nullOnDelete();
            $table->decimal('hardest_step_correct_rate', 5, 4)->nullable(); // 0.0000 a 1.0000
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_metrics');
    }
};
