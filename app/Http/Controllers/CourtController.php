<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourtRequest;
use App\Http\Requests\UpdateCourtRequest;
use App\Http\Services\ViaCepService;
use App\Models\Booking;
use App\Models\Court;
use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Http\Request;
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

        if(!empty($validated['sports'])) $court->sports()->sync($validated['sports']);
        
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

    public function favoriteCourt(string $id)
    {
        $user = auth()->user();
        $court = Court::findOrFail($id);

        if ($user->favorites()->where('court_id', $court->id)->exists()) {
            $user->favorites()->detach($court->id);
            return response()->json(['message' => 'Quadra removida dos favoritos!']);
        }

        $user->favorites()->attach($court->id);
        return response()->json(['message' => 'Quadra adicionada aos favoritos!']);
    }

    public function getFavorites()
    {
        return response()->json(auth()->user()->favorites()->with('sports')->with('photos')->get());
    }

    public function book(Request $request, string $id)
    {   
        $data = $request->validate([
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
        ]);

        $data['user_id'] = auth()->id();
        $data['court_id'] = $id;

        Booking::create($data);
        return response()->json(['message' => 'Reserva criada com sucesso!']);
    }

    public function getBookings()
    {
        return response()->json(Booking::with('court')->where('user_id', auth()->user()->id)->get());
    }

    public function approveBook(string $courtId)
    {
        $booking = Booking::findOrFail($courtId);
        $booking->update(['approved' => true]);
        return response()->json(['message' => 'Reserva aprovada com sucesso!']);
       return response()->json();
    }
}
