<?php

namespace App\Http\Controllers;

use App\Models\HR\Employee;
use App\Models\HR\Leave;
use App\Models\HR\Attendance;
use App\Models\HR\Violation;
use App\Models\Warehouse\Product;
use App\Models\Warehouse\PurchaseRequest;
use App\Models\Warehouse\WithdrawalOrder;
use App\Models\Students\Student;
use App\Models\Students\Document;
use App\Models\Students\ExitPermit;
use App\Models\Students\BehaviorViolation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $kpi = $this->getKpi();
        return view('dashboard.index', compact('kpi'));
    }

    private function getKpi(): array
    {
        $today    = now()->toDateString();
        $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];

        try {
            $hr = [
                'employees'  => Employee::where('status', 'active')->count(),
                'leaves'     => Leave::where('status', 'pending')->count(),
                'absent'     => Attendance::where('date', $today)->where('status', 'absent')->count(),
                'violations' => Violation::whereBetween('violation_date', $thisMonth)->count(),
            ];
        } catch (\Exception) {
            $hr = array_fill_keys(['employees', 'leaves', 'absent', 'violations'], null);
        }

        try {
            $wh = [
                'products'    => Product::where('status', 'active')->count(),
                'purchases'   => PurchaseRequest::where('status', 'pending')->count(),
                'withdrawals' => WithdrawalOrder::whereDate('created_at', $today)->count(),
                'low_stock'   => Product::lowStock()->count(),
            ];
        } catch (\Exception) {
            $wh = array_fill_keys(['products', 'purchases', 'withdrawals', 'low_stock'], null);
        }

        try {
            $sa = [
                'students'  => Student::where('status', 'active')->count(),
                'documents' => Document::where('status', 'pending')->count(),
                'permits'   => ExitPermit::where('status', 'pending')->count(),
                'behavior'  => BehaviorViolation::whereBetween('violation_date', $thisMonth)->count(),
            ];
        } catch (\Exception) {
            $sa = array_fill_keys(['students', 'documents', 'permits', 'behavior'], null);
        }

        return compact('hr', 'wh', 'sa');
    }
}
