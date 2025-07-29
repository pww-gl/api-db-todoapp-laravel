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
        Schema::table('todos', function (Blueprint $table) {
            $table->enum('visibility', ['private','public'])->default('private');   // For PGSQL => ALTER TABLE todos ADD COLUMN visibility VARCHAR CHECK (visibility in (private, public) DEFAULT = 'private'
            $table->boolean('is_edited')->default(FALSE);   // ALTER TABLE todos ADD COLUMN is_edited BOOLEAN DEFAULT FALSE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            //
        });
    }
};
