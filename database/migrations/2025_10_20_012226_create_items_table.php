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
        Schema::create('items', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->enum('type', ['film','livre','jeu','manga','anime','musique','serie']);
            $t->unsignedSmallInteger('year')->nullable();
            $t->string('author')->nullable();      // réal./auteur/studio
            $t->text('description')->nullable();
            $t->string('cover_path')->nullable();  // upload optionnel
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
