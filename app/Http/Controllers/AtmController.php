<?php

namespace App\Http\Controllers;

use App\Models\Atm;
use App\Models\Casette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AtmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            // Get atm by id with casette
            $atm = Atm::with('casette')->find($request->id); 
        } else {
            // Get all atm with casette
            $atm = Atm::with('casette')->get();
        }

        // Return response
        if ($atm != null) {
            return response_json(200, 'success', $atm);
        }
        
        return response_json(404, 'failed', 'There is no atm');
    }

    public function list()
    {
        $atm = Atm::all();

        // Return response
        if ($atm != null) {
            return response_json(200, 'success', $atm);
        }
        
        return response_json(404, 'failed', 'There is no atm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'information' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        $atm = Atm::create([
            'name' => $request->name,
            'information' => $request->information,
        ]);

        // Return error if failed save new atm
        if (!$atm) {
            return response_json(400, 'failed', 'Error saving atm data');
        }

        $data_casette = array(
            'atm_id' => $atm->id
        );

        if (isset($request->casette)) {
            $input_casette = $request->casette;

            foreach ($input_casette as $data) {
                if ($data['value'] != null) {
                    $data_casette[$data['name']] = $data['value'];
                }
            }
        }

        $casette = Casette::create($data_casette);

        // Return error if failed save new casette
        if (!$casette) {
            return response_json(400, 'failed', 'Error saving casette data');
        }

        return response_json(200, 'success', 'New ATM successfully added');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'information' => 'required',
            'casette' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        $atm = Atm::find($id);

        if ($atm == null) {
            return response_json(404, 'failed', 'Atm not found');
        }

        $atm->update([
            'name' => $request->name,
            'information' => $request->information,
        ]);

        // Return error if failed update atm
        if (!$atm) {
            return response_json(400, 'failed', 'Error saving atm data');
        }

        $input_casette = $request->casette;

        foreach ($input_casette as $data) {
            if ($data['value'] != null) {
                $data_casette[$data['name']] = $data['value'];
            }
        }

        $casette = $atm->casette()->update($data_casette);

        // Return error if failed update casette
        if (!$casette) {
            return response_json(400, 'failed', 'Error saving casette data');
        }

        return response_json(200, 'success', 'ATM data successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $atm = Atm::find($id);

        if ($atm != null) {
            $atm->casette()->delete();
            $atm->delete();

            return response_json(200, 'success', 'Atm deleted successfully');
        }

        return response_json(404, 'failed', 'Atm not found');
    }
}
