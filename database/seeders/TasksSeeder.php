<?php

namespace Database\Seeders;

use App\Models\Task as TaskDb;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    public function run()
    {
        TaskDb::create([
            'descricao' => 'Wireframes das páginas',
            'status' => 'completa',
            'data_inicial' => '2025-01-01',
            'data_final' => '2025-01-15',
            'projeto_id' => 1,
        ]);
        
        TaskDb::create([
            'descricao' => 'Desenvolvimento Frontend',
            'status' => 'incompleta',
            'data_inicial' => '2025-01-16',
            'data_final' => '2025-02-15',
            'projeto_id' => 1,
        ]);
        

        TaskDb::create([
            'descricao' => 'Prototipagem',
            'status' => 'completa',
            'data_inicial' => '2025-01-10',
            'data_final' => '2025-01-25',
            'projeto_id' => 2,
        ]);

        TaskDb::create([
            'descricao' => 'Design do Logo',
            'status' => 'completa',
            'data_inicial' => '2025-01-05',
            'data_final' => '2025-01-20',
            'projeto_id' => 3,
        ]);
    }
}