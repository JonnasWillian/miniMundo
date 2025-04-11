<?php

namespace Database\Seeders;

use App\Models\Projeto as ProjetoDb;
use Illuminate\Database\Seeder;

class ProjetosSeeder extends Seeder
{
    public function run()
    {
        ProjetoDb::create([
            'nome' => 'Projeto Website',
            'status' => 'ativo',
            'descricao' => 'Desenvolvimento do novo website corporativo',
            'orcamento' => 15000.00
        ]);
        
        ProjetoDb::create([
            'nome' => 'Projeto Mobile App',
            'status' => 'ativo',
            'descricao' => 'Aplicativo para iOS e Android',
            'orcamento' => 25000.00
        ]);
        
        ProjetoDb::create([
            'nome' => 'Redesign da Marca',
            'status' => 'ativo',
            'descricao' => 'Novo logo e identidade visual',
            'orcamento' => 8000.00
        ]);
    }
}