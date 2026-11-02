<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $coordinates = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);
        $ipAddress = $request->ip();

        if (! $ipAddress || filter_var($ipAddress, FILTER_VALIDATE_IP) === false) {
            abort(400, 'No fue posible determinar una dirección IP válida.');
        }

        DB::transaction(function () use ($request, $coordinates, $ipAddress): void {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);

            abort_unless($user->is_active && $user->isWorker(), 403);

            $now = now();
            $startOfDay = $now->copy()->startOfDay();
            $startOfTomorrow = $startOfDay->copy()->addDay();
            $todayRecords = $user->attendanceRecords()
                ->where('hora_servidor', '>=', $startOfDay)
                ->where('hora_servidor', '<', $startOfTomorrow)
                ->orderBy('hora_servidor')
                ->get();
            $lastRecord = $todayRecords->last();

            $type = match ($lastRecord?->tipo) {
                null => AttendanceRecord::TYPE_ENTRY,
                AttendanceRecord::TYPE_ENTRY => AttendanceRecord::TYPE_EXIT,
                default => null,
            };

            if ($type === null) {
                throw ValidationException::withMessages([
                    'attendance' => 'Ya registraste entrada y salida el día de hoy.',
                ]);
            }

            $user->attendanceRecords()->create([
                'tipo' => $type,
                'hora_servidor' => $now,
                'ip_address' => $ipAddress,
                'latitud' => $coordinates['latitud'],
                'longitud' => $coordinates['longitud'],
            ]);
        });

        return back()->with('status', 'Checada registrada correctamente con la hora del servidor.');
    }
}
