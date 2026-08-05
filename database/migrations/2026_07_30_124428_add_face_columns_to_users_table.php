<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('face_descriptor')
                ->nullable()
                ->after('profile_photo_path');

            $table->string('face_photo')
                ->nullable()
                ->after('face_descriptor');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'face_descriptor',
                'face_photo'
            ]);
        });
    }
};