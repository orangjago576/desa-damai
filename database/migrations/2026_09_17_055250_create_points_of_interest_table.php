<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_of_interest', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', [
                'kantor_desa',
                'rumah_kadus',
                'rumah_kepala_desa',
                'fasilitas_umum',
                'lainnya',
            ]);
            $table->text('address')->nullable();
            $table->string('hamlet')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('hamlet');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_of_interest');
    }
};
