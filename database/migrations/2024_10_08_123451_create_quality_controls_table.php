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
        Schema::create('quality_controls', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('template_id');
            $table->enum('status', ['UNCONTROLLED', 'CONTROLLED'])->default('UNCONTROLLED');
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->text('errors')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('quality_control_templates')->onDelete('restrict');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_controls');
    }
};
