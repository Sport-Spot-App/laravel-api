<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourtRequest;
use App\Http\Requests\UpdateCourtRequest;
use App\Http\Services\ViaCepService;
use App\Models\Court;
use App\Models\User;

class CourtController extends Controller
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
        return response()->json(Court::all());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourtRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        if(!auth()->user()->is_approved){
           return response()->json(['message' => 'Impossível cadastrar quadra, usuário não aprovado!'], 403);
        }
        $court = Court::create($validated);
        if(!empty($validated['sports']))$court->sports()->sync($validated['sports']);
        return response()->json(['message' => 'Quadra cadastrada com sucesso!', 'court' => $court]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Court::findOrFail($id));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourtRequest $request, Court $court)
    {
        $validated = $request->validated();
        $court->update($validated);
        if(!empty($validated['sports']))$court->sports()->sync($validated['sports']);
        return response()->json(['message' => 'Quadra atualizada com sucesso!', 'court' => $court]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Court $court)
    {
        if(!auth()->user()->isAdmin()){
            return response()->json(['message' => 'Apenas administradores podem deletar quadras!'], 403);
        };
        
        $court->delete();
    }

    public function getCourtsByOwner()
    {
        return response()->json(auth()->user()->courts);
    }

    public function findCep(string $cep)
    {
        return $this->viaCepService->findCep($cep);
    }
}
