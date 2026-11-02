<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DashboardController extends Controller
{
    public function root(): View
    {
        return view('dashboards.root', [
            'usersCount' => User::query()->count(),
            'activeWorkersCount' => User::query()->where('role', 'worker')->where('is_active', true)->count(),
            'todayRecordsCount' => $this->todayRecords()->count(),
            'records' => $this->latestRecords(),
        ]);
    }

    public function admin(): View
    {
        return view('dashboards.admin', [
            'activeWorkersCount' => User::query()->where('role', 'worker')->where('is_active', true)->count(),
            'todayRecordsCount' => $this->todayRecords()->count(),
            'records' => $this->latestRecords(),
        ]);
    }

    public function worker(): View
    {
        $user = User::query()->findOrFail(auth()->id());
        $today = now();
        $records = $user->attendanceRecords()
            ->where('hora_servidor', '>=', $today->copy()->startOfDay())
            ->where('hora_servidor', '<', $today->copy()->addDay()->startOfDay())
            ->orderBy('hora_servidor')
            ->get();
        $lastRecord = $records->last();

        return view('dashboards.worker', [
            'records' => $records,
            'nextType' => match ($lastRecord?->tipo) {
                null => AttendanceRecord::TYPE_ENTRY,
                AttendanceRecord::TYPE_ENTRY => AttendanceRecord::TYPE_EXIT,
                default => null,
            },
        ]);
    }

    private function todayRecords(): Builder
    {
        $now = now();

        return AttendanceRecord::query()
            ->where('hora_servidor', '>=', $now->copy()->startOfDay())
            ->where('hora_servidor', '<', $now->copy()->addDay()->startOfDay());
    }

    private function latestRecords(): Collection
    {
        return AttendanceRecord::query()
            ->with('user:id,name,username')
            ->latest('hora_servidor')
            ->take(20)
            ->get();
    }
}
