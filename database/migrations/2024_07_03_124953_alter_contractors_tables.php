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
        Schema::table('contractors', function (Blueprint $table) {
            $table->string('referral_alt')->nullable();
            $table->string('position_alt')->nullable();
            $table->string('phone_alt')->nullable();
            $table->string('email_alt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropColumn('referral_alt');
            $table->dropColumn('position_alt');
            $table->dropColumn('phone_alt');
            $table->dropColumn('email_alt');
        });
    }
};
