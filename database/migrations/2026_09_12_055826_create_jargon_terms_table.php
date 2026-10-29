<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_jargon_terms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jargon_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term');                    // e.g. "AAC"
            $table->string('full_form')->nullable();   // e.g. "Augmentative and Alternative Communication"
            $table->text('definition');
            $table->string('slug')->unique();
            $table->string('status')->default('draft');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jargon_terms');
    }
};
