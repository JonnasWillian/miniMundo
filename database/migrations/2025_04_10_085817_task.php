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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('status');
            $table->date('data_inicial');
            $table->date('data_final');
            $table->unsignedBigInteger('projeto_id');
            $table->unsignedBigInteger('tarefa_processadora')->nullable();;
            $table->foreign('projeto_id')->references('id')->on('projetos')->onDelete('cascade');
            $table->foreign('tarefa_processadora')->references('id')->on('projetos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
