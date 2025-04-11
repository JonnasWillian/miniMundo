<?php

namespace Database\Factories;

use App\Models\Projeto as ProjetoDb;
use App\Models\Task as TaskDb;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = TaskDb::class;

    public function definition(): array
    {
        $dataInicial = $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d');
        $dataFinal = $this->faker->dateTimeBetween($dataInicial, '+6 months')->format('Y-m-d');
        
        return [
            'descricao' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['Pendente', 'Em andamento', 'Concluído']),
            'data_inicial' => $dataInicial,
            'data_final' => $dataFinal,
            'projeto_id' => ProjetoDb::factory(),
            'tarefa_processadora' => null,
        ];
    }
}