<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('xp')->default(0);
            $table->integer('level')->default(1);
            $table->string('rank')->default('Recruit');
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'level', 'rank', 'wins', 'losses']);
        });
    }
};
