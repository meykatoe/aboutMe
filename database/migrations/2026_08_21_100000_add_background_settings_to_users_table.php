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
        Schema::table('users', function (Blueprint $table) {
            $table->string('background_color')->nullable()->after('avatar_path');
            $table->string('background_image_pc_path')->nullable()->after('background_color');
            $table->string('background_image_mobile_path')->nullable()->after('background_image_pc_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['background_color', 'background_image_pc_path', 'background_image_mobile_path']);
        });
    }
};
