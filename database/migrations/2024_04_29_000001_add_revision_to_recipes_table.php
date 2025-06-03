<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('recipes', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision'])->default('pending')->change();
            $table->text('revision_comment')->nullable();
        });
    }
    public function down() {
        Schema::table('recipes', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
            $table->dropColumn('revision_comment');
        });
    }
}; 