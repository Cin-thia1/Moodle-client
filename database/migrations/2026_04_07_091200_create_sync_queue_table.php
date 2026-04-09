<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table sync_queue pour gérer les opérations de synchronisation.
     * 
     * Cette table enqueue les opérations CREATE/UPDATE/DELETE qui doivent être
     * synchronisées avec Moodle lors de la prochaine connexion.
     */
    public function up(): void
    {
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            
            // Type d'opération
            $table->enum('operation', ['CREATE', 'UPDATE', 'DELETE'])->index();
            
            // Type d'entité (courses, sections, modules, participants, etc.)
            $table->string('entity_type', 50)->index();
            
            // ID de l'entité locale
            $table->unsignedBigInteger('entity_id')->index();
            
            // Payload JSON (données à envoyer à Moodle)
            $table->json('payload')->nullable();
            
            // Statut du traitement
            $table->enum('status', ['pending', 'processing', 'done', 'error'])->default('pending')->index();
            
            // Nombre de tentatives échouées
            $table->unsignedInteger('attempts')->default(0);
            
            // Message d'erreur en cas d'échec
            $table->text('error_msg')->nullable();
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            
            // Indexes pour optimization
            $table->index(['status', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->unique(['entity_type', 'entity_id', 'operation', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_queue');
    }
};
