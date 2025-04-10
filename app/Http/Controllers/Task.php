<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task as TaskDb;

class Task extends Controller
{
    public function create(Request $request)
    {
        try {
            $validateRequest = $request->validate([
                'descricao' => 'required|string',
                'status' => 'required|string',
                'data_inicial' => 'required|date',
                'data_final' => 'required|date',
                'projeto_id' => 'required|numeric',
                'tarefa_processadora' => 'nullable',
            ]);

            TaskDb::create($validateRequest);

            return response()->json(['message' => 'Tarefa cadastrada com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $task = TaskDb::findOrFail($id);
        
            $validateRequest = $request->validate([
                'descricao' => 'required|string',
                'status' => 'required|string',
                'data_inicial' => 'required|date',
                'data_final' => 'required|date',
                'projeto_id' => 'required|numeric',
                'tarefa_processadora' => 'nullable',
            ]);
        
            $task->update($validateRequest);
        
            return response()->json(['message' => 'Tarefa atualizada com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        }
        
    }

    public function destroy(Request $request, $id)
    {
        try {
            $task = TaskDb::findOrFail($id);
            $task->delete();

            return response()->json(['message' => 'Tarefa deletada com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        } catch (\Exception $error) {
            return response()->json(['error' => 'Tarefa não encontrado'], 201);
        }
    }
}
