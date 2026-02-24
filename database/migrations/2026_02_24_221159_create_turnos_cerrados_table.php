<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos_cerrados', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->tinyInteger('turno')->unsigned()->comment('0=dia, 1=noche');
            $table->foreignId('cerrado_por')->constrained('users')->restrictOnDelete();
            $table->timestamp('cerrado_en')->useCurrent();
            $table->text('observacion')->nullable();
            $table->unique(['fecha', 'turno']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos_cerrados');
    }
};
