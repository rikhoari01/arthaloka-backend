<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id) || isset($request->account)) {
            if (isset($request->id)) {
                // Get customer by id
                $customer = Customer::find($request->id); 
                
                if ($customer != null) {
                    return response_json(200, 'success', $customer);
                }
            } else if (isset($request->account)) {
                // Get customer by account
                $customer = Customer::where('account', $request->account)->get();
            }
        } else {
            // Get all customer
            $customer = Customer::all();
        }

        // Returm response
        if ($customer != null && count($customer) != 0) {
            return response_json(200, 'success', $customer);
        }
        
        return response_json(404, 'failed', 'There is no customer');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        if (isset($request->balance)) {
            $balance = $request->balance;
        } else {
            $balance = 0;
        }

        $customer = Customer::create([
            'account' => "ARTB-",
            'name' => $request->name,
            'balance' => $balance,
        ]);

        $customer->update([
            'account' => 'ARTB-' . str_pad($customer->id, 12, '0', STR_PAD_LEFT),
        ]);

        // return response
        if (!$customer) {
            return response_json(400, 'failed', 'Error saving customer data');
        }
        
        return response_json(200, 'success', $request->name . ' successfully saved as a customer');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'balance' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        $customer = Customer::find($id);

        if ($customer == null) {
            return response_json(404, 'failed', 'Customer not found');
        }

        $customer->update([
            'name' => $request->name,
            'balance' => $request->balance,
        ]);

        // return response
        if (!$customer) {
            return response_json(400, 'failed', 'Error saving customer data');
        }
        
        return response_json(200, 'success', $request->name . " data updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if ($customer != null) {
            $customer->delete();

            return response_json(200, 'success', 'Customer deleted successfully');
        }
        
        return response_json(404, 'failed', 'Customer not found');
    }
}
