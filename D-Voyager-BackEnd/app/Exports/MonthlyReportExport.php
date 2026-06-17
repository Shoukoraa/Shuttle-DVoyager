<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MonthlyReportExport implements FromView
{
    public function __construct(private array $reportData)
    {
    }

    public function view(): View
    {
        return view('admin.reports.monthly_excel', $this->reportData);
    }
}
