<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('slug')->unique();

            $table->string('location')->nullable();
            $table->string('location_type')->default('physical');
            $table->string('address')->nullable();
            $table->string('map_link')->nullable();
            $table->string('meeting_link', 1000)->nullable();
            $table->string('passcode')->nullable();

            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('draft'); // draft, published, cancelled
            $table->boolean('is_fathers_hub')->default(false); // 👈 NEW
            $table->string('image')->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The admin who created it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
