<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request, string $period): View
    {
        abort_unless(in_array($period, ['daily', 'monthly', 'yearly'], true), 404);
        abort_if($period === 'yearly' && ! $request->user()->isRoot(), 403);

        $date = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? now()->toDateString();
        $selectedDate = Carbon::createFromFormat('Y-m-d', $date, config('app.timezone'));

        [$start, $end] = match ($period) {
            'daily' => [$selectedDate->copy()->startOfDay(), $selectedDate->copy()->addDay()->startOfDay()],
            'monthly' => [$selectedDate->copy()->startOfMonth(), $selectedDate->copy()->addMonthNoOverflow()->startOfMonth()],
            'yearly' => [$selectedDate->copy()->startOfYear(), $selectedDate->copy()->addYear()->startOfYear()],
        };

        $records = AttendanceRecord::query()
            ->with('user:id,name,username')
            ->where('hora_servidor', '>=', $start)
            ->where('hora_servidor', '<', $end)
            ->latest('hora_servidor')
            ->paginate(30)
            ->withQueryString();

        return view('reports.index', [
            'period' => $period,
            'date' => $date,
            'records' => $records,
            'total' => AttendanceRecord::query()
                ->where('hora_servidor', '>=', $start)
                ->where('hora_servidor', '<', $end)
                ->count(),
        ]);
    }
}
