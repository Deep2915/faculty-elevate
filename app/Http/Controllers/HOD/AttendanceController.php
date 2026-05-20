<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\ClassLog;
use App\Models\TimetableEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $faculties       = User::where('role', 'faculty')->orderBy('name')->get();
        $facultyId       = $request->string('faculty_id')->toString();
        $semesterFilter  = $request->string('semester')->toString();

        if ($facultyId === '' && $faculties->isNotEmpty()) {
            $facultyId = (string) $faculties->first()->id;
        }

        $selectedFaculty = $faculties->firstWhere('id', $facultyId);

        // Timetable entries for this faculty
        $timetable = TimetableEntry::where('faculty_id', $facultyId)
            ->where('is_active', true)
            ->when($semesterFilter !== '', fn($q) => $q->where('semester', $semesterFilter))
            ->orderBy('day_of_week')
            ->get();

        // All semesters for filter
        $semesters = TimetableEntry::where('faculty_id', $facultyId)
            ->where('is_active', true)
            ->distinct('semester')
            ->pluck('semester')
            ->filter()
            ->values();

        // Class logs for this faculty
        $logs = ClassLog::where('faculty_id', $facultyId)
            ->orderByDesc('date')
            ->limit(100)
            ->get();

        // Score (last 120 days)
        $scoreData = ClassLog::computeScore($facultyId, Carbon::now()->subDays(120)->toDateString());

        // Monthly breakdown for chart (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthLogs = ClassLog::where('faculty_id', $facultyId)
                ->where('date', '>=', $month->copy()->startOfMonth())
                ->where('date', '<=', $month->copy()->endOfMonth())
                ->get();
            $monthlyData[] = [
                'label'      => $month->format('M Y'),
                'conducted'  => $monthLogs->where('status', 'conducted')->count(),
                'cancelled'  => $monthLogs->where('status', 'cancelled')->count(),
                'substituted'=> $monthLogs->where('status', 'substituted')->count(),
            ];
        }

        // Group logs by timetable entry for display
        $logsByEntry = $logs->groupBy('timetable_entry_id');

        return view('hod.attendance.index', compact(
            'faculties', 'facultyId', 'selectedFaculty',
            'timetable', 'semesters', 'semesterFilter',
            'logs', 'logsByEntry', 'scoreData', 'monthlyData'
        ));
    }

    public function overrideLog(Request $request, string $logId): RedirectResponse
    {
        $data = $request->validate([
            'status'  => ['required', 'in:conducted,cancelled,substituted'],
            'remarks' => ['nullable', 'string', 'max:400'],
        ]);

        $log = ClassLog::findOrFail($logId);
        $log->status        = $data['status'];
        $log->remarks       = $data['remarks'] ?? $log->remarks;
        $log->overridden_by = (string) auth()->id();
        $log->save();

        return redirect()->route('hod.attendance.index', ['faculty_id' => $log->faculty_id])
            ->with('status', 'Class log updated by HOD.');
    }

    public function addLog(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'timetable_entry_id' => ['required', 'string'],
            'date'               => ['required', 'date'],
            'status'             => ['required', 'in:conducted,cancelled,substituted'],
            'remarks'            => ['nullable', 'string', 'max:400'],
        ]);

        $entry = TimetableEntry::findOrFail($data['timetable_entry_id']);

        ClassLog::updateOrCreate(
            ['timetable_entry_id' => $data['timetable_entry_id'], 'date' => Carbon::parse($data['date'])->startOfDay()],
            [
                'faculty_id'     => $entry->faculty_id,
                'status'         => $data['status'],
                'remarks'        => $data['remarks'] ?? null,
                'logged_by'      => (string) auth()->id(),
                'overridden_by'  => (string) auth()->id(),
            ]
        );

        return redirect()->route('hod.attendance.index', ['faculty_id' => $entry->faculty_id])
            ->with('status', 'Class log added by HOD.');
    }

    public function destroyLog(string $id): RedirectResponse
    {
        $log = ClassLog::findOrFail($id);
        $facultyId = $log->faculty_id;
        $log->delete();

        return redirect()->route('hod.attendance.index', ['faculty_id' => $facultyId])
            ->with('status', 'Log deleted.');
    }
}
