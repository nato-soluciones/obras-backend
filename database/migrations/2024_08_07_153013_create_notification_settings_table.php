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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('Se usa para identificar que notificación enviar');
            $table->string('group');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['baja', 'media', 'alta']);
            $table->timestamps();

        });
       
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('notification_setting_id');
            
            $table->boolean('app_notifications')->default(false);
            $table->boolean('email_notifications')->default(false);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('notification_setting_id')->references('id')->on('notification_settings')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['user_id', 'notification_setting_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('user_notification_settings');
    }
};
