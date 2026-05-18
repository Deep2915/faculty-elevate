<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportGeneratorService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function download(ReportGeneratorService $generator): BinaryFileResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $path = $generator->generateAnnualReport($user);

        return response()->download($path, "FacultyElevate_Report_{$user->name}.pdf");
    }
}
