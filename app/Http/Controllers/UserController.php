<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function add(Request $request)
    {
        try {
            // Verificar se 'nivel_acesso_id' está presente e é uma string reconhecida
            if ($request->has('nivel_acesso_id') && is_string($request->input('nivel_acesso_id'))) {
                // Converte o string 'mentor' ou 'mentorado' para o id correspondente
                $nivelAcesso = strtolower($request->input('nivel_acesso_id'));
                if ($nivelAcesso === 'mentor') {
                    $request->merge(['nivel_acesso_id' => 1]);
                } elseif ($nivelAcesso === 'mentorado') {
                    $request->merge(['nivel_acesso_id' => 2]);
                } else {
                    // Se não for uma string válida, lanço erro de validação
                    throw ValidationException::withMessages([
                        'nivel_acesso_id' => 'O valor de nivel_acesso_id deve ser "mentor" ou "mentorado".'
                    ]);
                }
            }

            // Validação dos dados de entrada
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:4',
                'nivel_acesso_id' => 'required|integer',
                'bio' => 'nullable|string',
                'caminho_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            
            $validated['password'] = bcrypt($validated['password']);

            
            if ($request->hasFile('caminho_img')) {
                $image = $request->file('caminho_img');
                $caminhoImagem = $image->store('images', 'public');
                $validated['caminho_img'] = $caminhoImagem;
            }

            $user = User::create($validated);

            return response()->json($user);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro Interno'], 500);
        }
    }
    
    public function findAll()
    {
        $user = User::all();
        if (!$user) return response()->json(['message' => 'Nenhum Usuário encontrado'], 404);
        return response()->json($user);
    }

    public function findById($id){
        $user = User::find($id);
        if(!$user) return response()->json(['message' => 'Usuário não encontrado '], 404);
        return response()->json($user);
    }

   public function update(Request $request, $id)
   {
       try {
           $user = User::find($id);

           if (!$user) return response()->json(['message' => 'Usuário não encontrado'], 404);

           // Excluir os campos email, nivel_acesso_id e id da actualização
           $validated = $request->except(['email','nivel_acesso_id', 'id']);
           if ($request->filled('password')) {
                $validated['password'] = bcrypt($request->input('password'));
            } else {
                $validated['password'] = $user->password;
            }
        
           if ($request->hasFile('caminho_img')) {
               $image = $request->file('caminho_img');
               $caminhoImagem = $image->store('images', 'public');
               $validated['caminho_img'] = $caminhoImagem;
           }

           $user->update($validated);

           return response()->json($user);
       
       } catch (\Exception $e) {
           \Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
           return response()->json(['message' => 'Erro interno.'], 500);
       }
   }

    // Deletar um usuário
    public function deleteById($id)
    {
        //Procurar o user pelo id
        $user = User::find($id);
        if (!$user)  return response()->json(['message' => 'Usuário não encontrado'], 404);
        //caso seja encontrado, fazer o delete
        $user->delete();
        return response()->json(['message' => 'Usuário eliminado com sucesso']);
    }

}
