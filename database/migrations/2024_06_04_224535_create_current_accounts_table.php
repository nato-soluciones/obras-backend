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
        Schema::create('current_account_movement_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['CREDIT', 'DEBIT']);
            $table->enum('entity_type', ['CLIENT', 'PROVIDER']);
            $table->boolean('system_type')->default(false); // Indica si el cliente puede seleccionarlo o no
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('current_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->enum('entity_type', ['CLIENT', 'PROVIDER']);
            $table->unsignedBigInteger('entity_id');
            $table->enum('currency', ['ARS', 'USD']);
            $table->decimal('balance', 18, 2);
            $table->timestamps();

            // Restricción única para asegurar una cuenta por moneda
            $table->unique(['entity_id', 'entity_type', 'currency', 'project_id']);
            $table->foreign('project_id')->references('id')->on('obras')->onDelete('cascade');
        });

        Schema::create('current_account_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('current_account_id');
            $table->date('date');
            $table->unsignedInteger('movement_type_id');
            $table->string('description');
            $table->decimal('amount', 18, 2);
            $table->text('observation')->nullable();
            $table->string('reference_entity')->nullable();
            $table->unsignedInteger('reference_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Llave foránea
            $table->foreign('current_account_id')->references('id')->on('current_accounts')->onDelete('cascade');
            $table->foreign('movement_type_id')->references('id')->on('current_account_movement_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_account_movement_types');
        Schema::dropIfExists('current_accounts');
        Schema::dropIfExists('current_account_movements');
    }
};
