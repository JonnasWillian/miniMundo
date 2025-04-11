<?php

namespace Database\Factories;

use App\Models\Projeto as ProjetoDb;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjetoFactory extends Factory
{
    protected $model = ProjetoDb::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(['Planejamento', 'Em andamento', 'Concluído', 'Cancelado']),
            'descricao' => $this->faker->paragraph(),
            'orcamento' => $this->faker->randomFloat(2, 1000, 50000),
        ];
    }
}