<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceReportExport;
use App\Models\{AcademicYear, Classes};
use App\Services\{AttendanceService, ReportService};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly ReportService $reportService,
    ) {
    }

    public function index()
    {
        $classes = Classes::orderBy('name')->get();
        $academicYear = AcademicYear::active();
        $summary = $this->reportService->getSchoolSummary();
        return view('reports.index', compact('classes', 'academicYear', 'summary'));
    }

    public function daily(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $classId = $request->get('class_id');
        $classes = Classes::orderBy('name')->get();
        $report = $this->reportService->getDailyReport($date, $classId);
        return view('reports.daily', compact('report', 'date', 'classes', 'classId'));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $classId = $request->get('class_id');
        $classes = Classes::orderBy('name')->get();
        $report = $this->reportService->getMonthlyReport($month, $year, $classId);
        return view('reports.monthly', compact('report', 'month', 'year', 'classes', 'classId'));
    }

    public function recap(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $classId = $request->get('class_id');

        if (!$classId) {
            return redirect()->route('reports.recap', array_merge($request->all(), ['class_id' => Classes::first()?->id]));
        }

        $classes = Classes::orderBy('name')->get();
        $class = Classes::findOrFail($classId);
        $recap = $this->attendanceService->getMonthlyRecap($classId, $month, $year);

        return view('reports.recap', compact('recap', 'class', 'classes', 'month', 'year'));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $classId = $request->get('class_id');

        $data = [
            'recap' => $this->attendanceService->getMonthlyRecap($classId, $month, $year),
            'class' => Classes::find($classId),
            'month' => $month,
            'year' => $year,
            'school' => config('school'),
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.pdf-template', $data)
            ->setPaper('a4', 'landscape');

        $filename = "rekap-absensi-" . date('Y-m', mktime(0, 0, 0, $month, 1, $year)) . ".pdf";
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $classId = $request->get('class_id');

        $filename = "rekap-absensi-" . date('Y-m', mktime(0, 0, 0, $month, 1, $year)) . ".xlsx";
        return Excel::download(
            new AttendanceReportExport($classId, $month, $year),
            $filename
        );
    }
}