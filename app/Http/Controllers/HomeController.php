<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $item = TravelPackage::with(['galleries'])->get();
        return view('pages.home',[
            'items' => $item
        ]);
    }
}
