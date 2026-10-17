<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('name');
            $table->string('position');
            $table->string('photo_path')->nullable();
            $table->string('period')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['institution_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_members');
    }
};
