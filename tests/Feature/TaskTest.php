<?php

namespace Tests\Feature;

use App\Models\Projeto as ProjetoDb;
use App\Models\Task as TaskDb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $projeto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->projeto = ProjetoDb::factory()->create();
    }

    public function test_criar_task_com_sucesso()
    {
        $this->actingAs($this->user);

        $dados = [
            'descricao' => 'Task de Teste',
            'status' => 'Em andamento',
            'data_inicial' => '2025-01-01',
            'data_final' => '2025-01-31',
            'projeto_id' => $this->projeto->id,
            'tarefa_processadora' => null
        ];

        $response = $this->postJson('/api/task', $dados);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Tarefa cadastrada com sucesso']);

        $this->assertDatabaseHas('tasks', $dados);
    }

    public function test_criar_task_com_projeto_inexistente()
    {
        $this->actingAs($this->user);

        $dados = [
            'descricao' => 'Task de Teste',
            'status' => 'Em andamento',
            'data_inicial' => '2025-01-01',
            'data_final' => '2025-01-31',
            'projeto_id' => 999, 
            'tarefa_processadora' => null
        ];

        $response = $this->postJson('/api/task', $dados);

        $this->assertTrue(
            in_array($response->getStatusCode(), [422, 500]),
            "Response status deve ser 422 ou 500, mas é " . $response->getStatusCode()
        );

        if ($response->getStatusCode() == 500) {
            $this->assertStringContainsString('foreign key constraint', $response->getContent());
        }
    }

    public function test_atualizar_task_com_sucesso()
    {
        $this->actingAs($this->user);

        $task = TaskDb::factory()->create([
            'projeto_id' => $this->projeto->id
        ]);

        $dadosAtualizados = [
            'descricao' => 'Task Atualizada',
            'status' => 'Concluído',
            'data_inicial' => '2025-02-01',
            'data_final' => '2025-02-28',
            'projeto_id' => $this->projeto->id,
            'tarefa_processadora' => null
        ];

        $response = $this->putJson("/api/task/{$task->id}", $dadosAtualizados);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Tarefa atualizada com sucesso']);

        $this->assertDatabaseHas('tasks', $dadosAtualizados);
    }

    public function test_atualizar_task_inexistente()
    {
        $this->actingAs($this->user);

        $dadosAtualizados = [
            'descricao' => 'Task Atualizada',
            'status' => 'Concluído',
            'data_inicial' => '2025-02-01',
            'data_final' => '2025-02-28',
            'projeto_id' => $this->projeto->id,
            'tarefa_processadora' => null
        ];

        $response = $this->putJson("/api/task/999", $dadosAtualizados);

        $response->assertStatus(404);
    }

    public function test_deletar_task_com_sucesso()
    {
        $this->actingAs($this->user);

        $task = TaskDb::factory()->create([
            'projeto_id' => $this->projeto->id
        ]);

        $response = $this->deleteJson("/api/task/{$task->id}");

        $response->assertStatus(201)
                ->assertJson(['message' => 'Tarefa deletada com sucesso']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_deletar_task_inexistente()
    {
        $this->actingAs($this->user);

        $response = $this->deleteJson("/api/task/999");

        $response->assertStatus(201)
                ->assertJson(['error' => 'Tarefa não encontrado']);
    }
}