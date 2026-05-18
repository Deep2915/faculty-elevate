<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportGeneratorService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function download(string $userId, ReportGeneratorService $generator): BinaryFileResponse
    {
        $faculty = User::findOrFail($userId);
        $path    = $generator->generateAnnualReport($faculty);

        return response()->download($path, "FacultyReport_{$faculty->name}.pdf");
    }
}
