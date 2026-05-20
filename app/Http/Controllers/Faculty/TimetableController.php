<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ClassLog;
use App\Models\TimetableEntry;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimetableController extends Controller
{
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function index(): View
    {
        $userId  = (string) auth()->id();
        $entries = TimetableEntry::where('faculty_id', $userId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get();

        // Today's scheduled classes
        $todayName    = Carbon::now()->format('l');
        $todayEntries = $entries->where('day_of_week', $todayName)->values();

        // Recent class logs (last 14 days)
        $recentLogs = ClassLog::where('faculty_id', $userId)
            ->where('date', '>=', Carbon::now()->subDays(14))
            ->orderByDesc('date')
            ->get();

        // Attendance score (current semester ≈ last 120 days)
        $scoreData = ClassLog::computeScore($userId, Carbon::now()->subDays(120)->toDateString());

        // Weekly grid
        $weekGrid = [];
        foreach (self::DAYS as $day) {
            $weekGrid[$day] = $entries->where('day_of_week', $day)->values();
        }

        // Dropdown options
        $subjects   = TimetableEntry::subjectOptions();
        $timeSlots  = TimetableEntry::timeSlotOptions();
        $semesters  = TimetableEntry::semesterOptions();
        $rooms      = TimetableEntry::roomOptions();
        $sections   = TimetableEntry::sectionOptions();

        return view('faculty.timetable', compact(
            'entries', 'todayEntries', 'recentLogs', 'scoreData', 'weekGrid',
            'subjects', 'timeSlots', 'semesters', 'rooms', 'sections'
        ));
    }

    public function storeTimetable(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject'     => ['required', 'string', 'max:200'],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'time_slot'   => ['required', 'string', 'max:50'],
            'semester'    => ['required', 'string', 'max:60'],
            'section'     => ['required', 'string', 'max:20'],
            'room'        => ['nullable', 'string', 'max:60'],
        ]);

        $userId = (string) auth()->id();

        // ── Clash Detection ──────────────────────────────────────────────
        // Rule: same section + same day + same time slot = conflict
        $clash = TimetableEntry::where('is_active', true)
            ->where('section', $data['section'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('time_slot', $data['time_slot'])
            ->where('faculty_id', '!=', $userId)   // other teachers
            ->first();

        if ($clash) {
            return back()->withInput()->withErrors([
                'time_slot' => "Clash! Section {$data['section']} already has a class at {$data['time_slot']} on {$data['day_of_week']}.",
            ]);
        }

        // Also check if THIS faculty already has this slot (any section)
        $selfClash = TimetableEntry::where('is_active', true)
            ->where('faculty_id', $userId)
            ->where('day_of_week', $data['day_of_week'])
            ->where('time_slot', $data['time_slot'])
            ->first();

        if ($selfClash) {
            return back()->withInput()->withErrors([
                'time_slot' => "You already have {$selfClash->subject} at this time on {$data['day_of_week']}.",
            ]);
        }

        TimetableEntry::create([
            ...$data,
            'faculty_id' => $userId,
            'is_active'  => true,
        ]);

        return redirect()->route('faculty.timetable')->with('status', "{$data['subject']} added to timetable for {$data['section']}.");
    }

    public function destroyTimetable(string $id): RedirectResponse
    {
        $entry = TimetableEntry::findOrFail($id);
        if ($entry->faculty_id !== (string) auth()->id()) {
            abort(403);
        }
        $entry->is_active = false;
        $entry->save();

        return redirect()->route('faculty.timetable')->with('status', 'Class removed from timetable.');
    }

    public function markClass(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'timetable_entry_id' => ['required', 'string'],
            'date'               => ['required', 'date'],
            'status'             => ['required', 'in:conducted,cancelled,substituted'],
            'remarks'            => ['nullable', 'string', 'max:400'],
        ]);

        $entry = TimetableEntry::findOrFail($data['timetable_entry_id']);
        if ($entry->faculty_id !== (string) auth()->id()) {
            abort(403);
        }

        // Prevent duplicate logs for same entry+date — use updateOrCreate
        ClassLog::updateOrCreate(
            [
                'timetable_entry_id' => $data['timetable_entry_id'],
                'date'               => Carbon::parse($data['date'])->startOfDay(),
            ],
            [
                'faculty_id' => (string) auth()->id(),
                'status'     => $data['status'],
                'remarks'    => $data['remarks'] ?? null,
                'logged_by'  => (string) auth()->id(),
            ]
        );

        return redirect()->route('faculty.timetable')
            ->with('status', "Class marked as {$data['status']}.");
    }
}
