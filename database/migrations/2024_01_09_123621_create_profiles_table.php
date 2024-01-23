<?php

use App\Enums\Sex;
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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('name', 20);
            $table->date('birthday');
            $table->string('bio', 500)
                ->nullable()
                ->default(null);
            $table->enum('sex', [
                Sex::f,
                Sex::m,
                Sex::x,
            ])->default(Sex::x);
            $table->boolean('i_f')->nullable(false)->default(false);
            $table->boolean('i_m')->nullable(false)->default(false);
            $table->boolean('i_x')->nullable(false)->default(false);
            $table->double('latitude', 10,8)->nullable()->default(null);
            $table->double('longitude', 11,8)->nullable()->default(null);
            $table->unsignedTinyInteger('height')->nullable()->default(null);
            $table->boolean('active')->default(false);
            $table->unsignedTinyInteger('maxDistance')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
