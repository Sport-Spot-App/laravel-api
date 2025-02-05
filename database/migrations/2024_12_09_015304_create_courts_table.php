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
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('zip_code');
            $table->string('street');
            $table->string('number');
            $table->string('logradouro')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('localidade')->nullable();
            $table->string('estado')->nullable();
            $table->decimal('coordinate_x', 10, 7)->nullable();
            $table->decimal('coordinate_y', 10, 7)->nullable();
            $table->decimal('price_per_hour', 8, 2);
            $table->string('initial_hour');
            $table->strng('final_hour');
            $table->json('work_days')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
