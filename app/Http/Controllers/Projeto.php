<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

use App\Models\Projeto as ProjetoDb;

class Projeto extends Controller
{
    public function view(){
        $projetos = ProjetoDb::orderBy('created_at', 'desc')->get();

        return response()->json($projetos);
    }

    public function create(Request $request)
    {
        try {
            $validateRequest = $request->validate([
                'nome' => 'required|string',
                'status' => 'required|string',
                'descricao' => 'nullable|string',
                'orcamento' => 'nullable|numeric',
            ]);

            ProjetoDb::create($validateRequest);

            return response()->json(['message' => 'Projeto cadastrado com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $projeto = ProjetoDb::findOrFail($id);
        
            $validateRequest = $request->validate([
                'nome' => 'required|string',
                'status' => 'required|string',
                'descricao' => 'nullable|string',
                'orcamento' => 'nullable|numeric',
            ]);
        
            $projeto->update($validateRequest);
        
            return response()->json(['message' => 'Projeto atualizado com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        }
        
    }

    public function destroy(Request $request, $id)
    {
        try {
            $projeto = ProjetoDb::findOrFail($id);
            $projeto->delete();

            return response()->json(['message' => 'Projeto deletado com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        } catch (\Exception $error) {
            return response()->json(['error' => 'Projeto não encontrado'], 201);
        }
    }
}
