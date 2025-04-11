<?php

namespace Tests\Feature;

use App\Models\Projeto as ProjetoDb;
use App\Models\Task as TaskDb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_visualizar_projetos()
    {
        $this->actingAs($this->user);

        $projetos = ProjetoDb::factory()->count(3)->create();

        $response = $this->getJson('/api/projeto');

        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_criar_projeto_com_sucesso()
    {
        $this->actingAs($this->user);

        $dados = [
            'nome' => 'Projeto Teste',
            'status' => 'Em andamento',
            'descricao' => 'Descrição do projeto teste',
            'orcamento' => 1000.00
        ];

        $response = $this->postJson('/api/projeto', $dados);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Projeto cadastrado com sucesso']);

        $this->assertDatabaseHas('projetos', $dados);
    }

    public function test_atualizar_projeto_com_sucesso()
    {
        $this->actingAs($this->user);

        $projeto = ProjetoDb::factory()->create();

        $dadosAtualizados = [
            'nome' => 'Projeto Atualizado',
            'status' => 'Concluído',
            'descricao' => 'Nova descrição',
            'orcamento' => 2000.00
        ];

        $response = $this->putJson("/api/projeto/{$projeto->id}", $dadosAtualizados);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Projeto atualizado com sucesso']);

        $this->assertDatabaseHas('projetos', $dadosAtualizados);
    }

    public function test_atualizar_projeto_inexistente()
    {
        $this->actingAs($this->user);

        $dadosAtualizados = [
            'nome' => 'Projeto Atualizado',
            'status' => 'Concluído',
            'descricao' => 'Nova descrição',
            'orcamento' => 2000.00
        ];

        $response = $this->putJson("/api/projeto/999", $dadosAtualizados);

        $response->assertStatus(404);
    }

    public function test_deletar_projeto_com_sucesso()
    {
        $this->actingAs($this->user);

        $projeto = ProjetoDb::factory()->create();

        $response = $this->deleteJson("/api/projeto/{$projeto->id}");

        $response->assertStatus(201)
                ->assertJson(['message' => 'Projeto deletado com sucesso']);

        $this->assertDatabaseMissing('projetos', ['id' => $projeto->id]);
    }

    public function test_deletar_projeto_com_tasks()
    {
        $this->actingAs($this->user);

        $projeto = ProjetoDb::factory()->create();
        TaskDb::factory()->create(['projeto_id' => $projeto->id]);

        $response = $this->deleteJson("/api/projeto/{$projeto->id}");

        $response->assertStatus(400)
                ->assertJson(['error' => 'O projeto possui tasks inacabadas']);

        $this->assertDatabaseHas('projetos', ['id' => $projeto->id]);
    }
}