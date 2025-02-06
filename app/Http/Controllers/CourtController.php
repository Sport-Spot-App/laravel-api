<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourtRequest;
use App\Http\Requests\UpdateCourtRequest;
use App\Http\Services\ViaCepService;
use App\Models\Booking;
use App\Models\Court;
use App\Models\GalleryPhoto;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        $validated['work_days'] = json_encode($validated['work_days']); 
        if($user->isAthlete()){
            return response()->json(['message' => 'Apenas proprietários de quadras podem cadastrar quadras!'], 403);
        }

        $validated['user_id'] = $user->id;
        $validated['price_per_hour'] = (float) $validated['price_per_hour'];

        if(!auth()->user()->is_approved){
           return response()->json(['message' => 'Impossível cadastrar quadra, usuário não aprovado!'], 403);
        }

        $court = Court::create($validated);

        if($request->hasFile('photos')){
            $this->savePhotos($request->file('photos') ?? [], $court->id);
        }

        if(!empty($validated['sports'])) $court->sports()->sync($validated['sports']);
        $this->getGeocode($court);
        return response()->json(['message' => 'Quadra cadastrada com sucesso!', 'court' => $court]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $court = Court::find($id)->with('sports')->with('photos')->get();
        return response()->json($court);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourtRequest $request, Court $court)
    {
        $validated = $request->validated();
        $validated['price_per_hour'] = (float) $validated['price_per_hour'];
        $validated['work_days'] = json_encode($validated['work_days']); 
        $court->update($validated);
        
        if($request->hasFile('photos')){
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
            'bookings' => ['required', 'array'],
            'bookings.*.start_datetime' => ['required'],
            'bookings.*.end_datetime' => ['required'],
        ]);

        $userId = auth()->id();

        foreach ($data['bookings'] as $timeSlot) {
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s.u', $timeSlot['start_datetime'])->format('Y-m-d H:i:s');
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s.u', $timeSlot['end_datetime'])->format('Y-m-d H:i:s');

            $existingBooking = Booking::where('court_id', $id)
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('start_datetime', [$startDateTime, $endDateTime])
                        ->orWhereBetween('end_datetime', [$startDateTime, $endDateTime]);
                })
                ->exists();

            if ($existingBooking) {
               return response()->json(['message' => "O horário de $startDateTime às $endDateTime já está reservado!"], 400);
            }

            Booking::create([
                'user_id' => $userId,
                'court_id' => $id,
                'start_datetime' => $startDateTime,
                'end_datetime' => $endDateTime,
                'status' => 0
            ]);
        }

        return response()->json(['message' => 'Reserva(s) criada(s) com sucesso!']);
    }

    public function getBookings()
    {
        $user = auth()->user();

        if($user->isOwner()){
            $bookingsAsOwner = Booking::with('user', 'court')
            ->whereHas('court', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

            return response()->json(['bookings' => $bookingsAsOwner]);
        }

        $bookingsAsAthlete = Booking::with('court')
            ->where('user_id', $user->id)
            ->get();
        return response()->json(['bookings' => $bookingsAsAthlete]);
    }

    public function getBlockedDaysByOwner(string $ownerId, string $courtId)
    {
        try {
            $blockedBookings = Booking::where('court_id', $courtId)->where('user_id', $ownerId)->get();
            return response()->json(['bookings' => $blockedBookings]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error ao buscar dias bloqueados', 'error' => $e->getMessage()], 404);
        }
        
    }

    public function approveBook(string $bookingId, int $status)
    {
        $booking = Booking::where('id', $bookingId)->first();
        
        if(auth()->user()->isAthlete() && $booking->court->user_id != auth()->user()->id){
            return response()->json(['message' => 'Apenas proprietários de quadras podem aprovar/reprovar reservas!'], 403);
        }
        
        $booking->status = $status;
        $booking->save();
        return response()->json(['message' => 'Reserva atualizada com sucesso!']);
    }

    public function getGeocode(Court $court)
    {
        $address = $court->zip_code. ' ' . $court->number . ' ' . $court->street;

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . env('GOOGLE_API_KEY');
        $response = Http::get($url);

        if (!$response->successful()) {
            return response()->json(['error' => 'Erro ao buscar coordenadas no geocoding API'], 500);
        } 

        $data = $response->json();
        $coordinates =  array_column($data['results'], 'geometry');
        $court->coordinate_x = $coordinates[0]['location']['lat'];
        $court->coordinate_y = $coordinates[0]['location']['lng'];
        $court->save();
    }
}
