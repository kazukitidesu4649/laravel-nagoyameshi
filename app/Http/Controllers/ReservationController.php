<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct() {
        if (auth('admin')->check()){
            return redirect()->route('admin.home');
        }
    }

    // indexアクション（予約一覧ページ）
    public function index(Reservation $reservation){
        
        $reservations = Reservation::where('user_id', auth()->id())
            ->orderBy('reserved_datetime', 'desc')
            ->paginate(15);
        
            return view('reservations.index', compact('reservations'));
    }

    public function create(Restaurant $restaurant) {

        return view('reservations.create', compact('restaurant'));
    }

    public function store(Request $request, Restaurant $restaurant) {

        $validated = $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|integer|between:1,50',
        ]);

        $reserved_datetime = $validated['reservation_date'] . ' ' . $validated['reservation_time'];

        $reservation = new Reservation([
            'reserved_datetime' => $reserved_datetime,
            'number_of_people' => $validated['number_of_people'],
            'restaurant_id' => $restaurant->id,
            'user_id' => auth()->id(),
        ]);

        $reservation->save();

        return redirect()->route('reservations.index')
            ->with('with_message', '予約が完了しました。');
    }

    public function destroy($id) {

        $reservation = Reservation::findOrFail($id);
        
        if ($reservation->user_id !== auth()->id()) {
            return redirect()->route('reservations.index')
                ->with('flash_message', '予約をキャンセルしました。');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('with_message', '予約をキャンセルしました。');
    }
}
