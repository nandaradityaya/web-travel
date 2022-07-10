<?php

namespace App\Http\Controllers;

use Mail;
use App\Mail\TransactionSuccess;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TravelPackage;

use Carbon\Carbon; //fungsinya untuk memformat tanggal

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //proses untuk masukin id user

class CheckoutController extends Controller //function di bawah di samain dengan routes web.php
{
    public function index(Request $request, $id)
    {
        $item = Transaction::with(['details','travel_package','user'])->findOrFail($id); //di sini panggil models transaction, find or fail untuk klo ketemu ya munculin klo ga ketemu ya 404
        return view('pages.checkout',[
            'item' => $item
        ]);
    }

    public function process(Request $request, $id)
    {
        $travel_package = TravelPackage::findOrFail($id);

        $transaction = Transaction::create([
            'travel_packages_id' => $id,
            'users_id' => Auth::user()->id, //ambil id user yang sedang login
            'additional_visa' => 0,
            'transactions_total' => $travel_package->price, //menyesuaikan harga dari travel package
            'transactions_status' => 'IN_CART'
        ]);

        TransactionDetail::create([ //ini untuk nambahin orang lain selain kita(pendaftar)
            'transactions_id' => $transaction->id,
            'username' => Auth::user()->username,
            'nationality' => 'ID',
            'is_visa' => false,
            'doe_passport' => Carbon::now()->addYears(5)
        ]);

        return redirect()->route('checkout', $transaction->id);

    }

    public function remove(Request $request, $detail_id)
    {
        $item = TransactionDetail::findOrFail($detail_id);

        $transaction = Transaction::with(['details', 'travel_package'])
            ->findOrFail($item->transactions_id);
        
        if($item->is_visa)
        {
            $transaction->transactions_total -= 190;
            $transaction->additional_visa -= 190;
        }

        $transaction->transactions_total -= $transaction->travel_package->price;

        $transaction->save();
        $item->delete();

        return redirect()->route('checkout', $item->transactions_id);
    }

    public function create(Request $request, $id)
    {
        $request->validate([ //validasi data yg masuk dari user
            'username' => 'required|string|exists:users,username',
            'is_visa' => 'required|boolean',
            'doe_passport' => 'required'
        ]);

        $data = $request->all();
        $data['transactions_id'] = $id;

        TransactionDetail::create($data); //untuk insert datanya

        $transaction = Transaction::with(['travel_package'])->find($id); //untuk ngambil data yang udah di masukin

        if($request->is_visa) //untuk update bayaran visa untuk orang yang baru di daftarin
        {
            $transaction->transactions_total += 190;
            $transaction->additional_visa += 190;
        }

        $transaction->transactions_total += $transaction->travel_package->price; //artinya di tambah dengan harga travel packagenya

        $transaction->save();

        return redirect()->route('checkout', $id);
    }

    public function success(Request $request, $id)
    {
        $transaction = Transaction::with(['details','travel_package.galleries',
        'user'])->findOrFail($id);
        $transaction->transactions_status = 'PENDING';

        $transaction->save();

        

        //kirim email e-ticket ke usernya
        Mail::to($transaction->user)->send(
            new TransactionSuccess($transaction)
        );
        
        return view('pages.success');
    }
}
