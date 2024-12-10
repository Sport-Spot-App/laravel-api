<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourtRequest;
use App\Http\Requests\UpdateCourtRequest;
use App\Http\Services\ViaCepService;
use App\Models\Court;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Court $court)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourtRequest $request, Court $court)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Court $court)
    {
        //
    }

    public function findCep(string $cep)
    {
        return $this->viaCepService->findCep($cep);
    }
}
