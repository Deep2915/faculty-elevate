<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ClassLog;
use App\Models\TimetableEntry;
use Carbon\Carbon;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        // Redirect to timetable page since that's the main attendance interface
        return redirect()->route('faculty.timetable');
    }
}
