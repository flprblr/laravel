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
        Schema::create('order_status_updates', function (Blueprint $table) {
            $table->id();
            $table->string('ecommerce')->index();
            $table->enum('status', ['Pendiente', 'Actualizado', 'Error'])->default('Pendiente')->index();
            $table->unsignedBigInteger('order_number')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_updates');
    }
};
