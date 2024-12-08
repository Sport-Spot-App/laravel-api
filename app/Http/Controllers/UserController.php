<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Services\ViaCepService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $viaCepService;

    public function __construct(ViaCepService $viaCepService)
    {
        $this->viaCepService = $viaCepService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        if($request->hasFile('photo')){
            $data['photo'] = $request->file('photo')->store('images/users', 'public');
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
        $user = User::findOrFail($id);
        return response()->json($user);   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        
    }

    public function findCep(string $cep)
    {
        return $this->viaCepService->findCep($cep);
    }


}
