<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourtRequest;
use App\Http\Requests\UpdateCourtRequest;
use App\Http\Services\ViaCepService;
use App\Models\Court;
use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
        return response()->json(Court::with('sports')->with('photos')->get());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourtRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        if($user->isAthlete()){
            return response()->json(['message' => 'Apenas proprietários de quadras podem cadastrar quadras!'], 403);
        }

        $validated['user_id'] = $user->id;
        
        if(!auth()->user()->is_approved){
           return response()->json(['message' => 'Impossível cadastrar quadra, usuário não aprovado!'], 403);
        }

        $court = Court::create($validated);
        if($request->hasFile('photos')){
            $this->savePhotos($request->photos, $court->id);
        }
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
        
        if($request->has('photos')){
            $this->savePhotos($request->photos, $court->id);
        }

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

        if ($court->photos()->exists()) {
            $photoPaths = $court->photos->pluck('path')->toArray();
            Storage::disk('public')->delete($photoPaths); 
        }

        $court->delete();
        return response()->json(['message' => 'Quadra deletada com sucesso!']);
    }

    public function getCourtsByOwner()
    {
        return response()->json(auth()->user()->courts()->with('sports')->with('photos')->get());
    }

    public function findCep(string $cep)
    {
        return $this->viaCepService->findCep($cep);
    }

    public function savePhotos(array $photos, string $courtId)
    {

        GalleryPhoto::where('court_id', $courtId)->delete();

        foreach ($photos as $photo) {
            $file = $photo->store('images/courts', 'public');
            GalleryPhoto::create([
                'name' => $photo->getClientOriginalName(),
                'court_id' => $courtId,
                'path' => $file
            ]);
        }
    }

}
