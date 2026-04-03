<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('zip')->nullable()->after('role');
            $table->string('city')->nullable()->after('zip');
            $table->string('street')->nullable()->after('city');
            $table->string('note')->nullable()->after('street');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['zip', 'city', 'street', 'note']);
        });
    }
};
