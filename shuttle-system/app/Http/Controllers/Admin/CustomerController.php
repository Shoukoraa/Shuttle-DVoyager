<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of all customers.
     */
    public function index()
    {
        return response()->json(Customer::with('user')->get());
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return response()->json($customer->load('user', 'bookings'));
    }

    /**
     * Remove the specified customer and their user account from storage.
     */
    public function destroy(Customer $customer)
    {
        return DB::transaction(function () use ($customer) {
            $user = $customer->user;
            
            // Hapus profil customer
            $customer->delete();
            
            // Hapus juga akun usernya secara permanen
            if ($user) {
                $user->delete();
            }
            
            return response()->json(['message' => 'Customer and associated user deleted successfully']);
        });
    }
}
