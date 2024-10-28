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
            $table->enum('status', ['UNCONTROLLED', 'CONTROLLED_OK', 'CONTROLLED_WITH_ERRORS'])->default('UNCONTROLLED');
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->text('comments')->nullable();
            $table->unsignedInteger('made_by_id')->comment('Usuario que realizo el ultimo control');
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
