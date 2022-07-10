<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TravelPackage;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.admin.dashboard',[
            'travel_package' => TravelPackage::count(),
            'transactions' => Transaction::count(),
            'transactions_pending' => Transaction::where('transactions_status', 'PENDING')->count(),
            'transactions_success' => Transaction::where('transactions_status', 'SUCCESS')->count()
        ]);
    }
}
