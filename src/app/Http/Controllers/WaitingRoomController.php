<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;

class WaitingRoomController extends Controller
{
    public function __invoke(AppointmentService $appointments)
    {
        return view('waiting-room.index', [
            'appointments' => $appointments->waitingRoom(now()->toDateString()),
        ]);
    }
}
