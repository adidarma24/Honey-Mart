<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; 

class TransactionController extends Controller
{
public function index(Request $request)
{
    $query = Order::with('user', 'orderItems', 'shippingAddress'); 

    if ($search = $request->get('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('code', 'like', '%' . $search . '%')
              ->orWhere('user_id', $search);
        });
    }

    if ($paymentStatus = $request->get('payment_status')) {
        $query->where('payment_status', $paymentStatus);
    }

    if ($status = $request->get('status')) {
        $query->where('status', $status);
    }

    $transactions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

    return view('admin.transactions.index', compact('transactions'));
}
    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
            'status' => 'required|in:pending,processing,completed,canceled',
        ]);

        // Cari Transaksi berdasarkan ID
        $transaction = Order::findOrFail($id);

        // Update status
        $transaction->payment_status = $request->payment_status;
        $transaction->status = $request->status;
        $transaction->save();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.transactions.index')
                         ->with('success', 'Status Transaksi ' . $transaction->code . ' berhasil diperbarui.');
    }
}