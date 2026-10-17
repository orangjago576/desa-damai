<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('important_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('work_area')->nullable();
            $table->string('phone_number');
            $table->enum('number_type', ['dinas', 'pribadi'])->default('dinas');
            $table->boolean('is_approved_for_publication')->default(false);
            $table->date('last_verified_at')->nullable();
            $table->timestamps();

            $table->index('is_approved_for_publication');
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('important_contacts');
    }
};
