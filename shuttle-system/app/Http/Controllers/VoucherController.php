<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        // Return only unexpired vouchers
        $vouchers = Voucher::where('expiry_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($vouchers);
    }
}
