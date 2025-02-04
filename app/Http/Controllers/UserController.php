<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return response()->json(User::all());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        if($request->hasFile('photo')){
            $validated['photo'] = $request->file('photo')->store('images/users', 'public');
        }

        $user = User::create($validated);
        event(new Registered($user));

        Auth::login($user);
        return response()->json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(User::findOrFail($id));  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('storage/images/users', 'public');
        }
        
        $user->update($validated);
        return response()->json(['message' => 'Usuário Atualizado com sucesso!', 'user' => $user], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if(!$user){
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        if($user->photo){
            Storage::disk('public')->delete($user->photo);
        }
        
        $user->delete();
        return response()->json(['message' => 'Usuário deletado com sucesso!']);
    }

    public function changeApproveStatus(string $id)
    {
        $user = User::findOrFail($id);

        if(!auth()->user()->isAdmin()){
            return response()->json(['message' => 'Apenas Administradores podem aprovar usuários'], 403);
        }

        if(!$user){
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        if(!$user->isOwner()){
            return response()->json(['message' => 'Apenas usuários dono de quadras podem ser aprovados!'], 403);
        }

        $user->is_approved = !$user->is_approved;
        $user->save();
        return response()->json(['message' => 'Usuário aprovado com sucesso!']);
    }

    public function updatePassword(Request $request)
    {
        if(auth()->user()->id !== $request->user()->id){
            return response()->json(['status' => 'Só pode atualizar sua própria senha!'], 403);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return response()->json(['status' => 'Senha atual não confere!'], 400);
        }

        $user = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json(['status' => 'Senha atualizada com sucesso'], 200);
    }

}
