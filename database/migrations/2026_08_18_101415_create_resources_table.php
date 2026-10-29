<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('type')->default('file');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('external_url', 1000)->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_fathers_hub')->default(false); // 👈 NEW
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('status');
            $table->index('type');
            $table->index('is_fathers_hub'); // 👈 NEW — handy for the fathers hub page query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
