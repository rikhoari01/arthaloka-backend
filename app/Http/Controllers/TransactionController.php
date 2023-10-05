<?php

namespace App\Http\Controllers;

use App\Models\Atm;
use App\Models\Casette;
use App\Models\Customer;
use App\Models\HistoryDetail;
use App\Models\HistoryHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if (isset($request->id)) {
            // Get history by id
            $history = HistoryHeader::with(['detail', 'atm', 'customer'])->find($request->id); 
        } else if (isset($request->customer_id)) {
            // Get history by customer id
            $history = Customer::with('history.atm')->find($request->customer_id);
        } else {
            // Get all history with casette
            $history = HistoryHeader::with(['atm', 'customer'])->get();
        }

        // Returm response
        if ($history != null) {
            return response_json(200, 'success', $history);
        }
        
        return response_json(404, 'failed', 'There is no transaction history');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_account' => 'required',
            'atm_id' => 'required',
            'amount_withdraw' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        $customer = Customer::where('account', $request->customer_account)->get()->first();
        $atm = Atm::find($request->atm_id);

        // Return error if customer not found
        if ($customer == null) {
            return response_json(400, 'failed', 'Customer account not found');
        }
        // Return error if atm not found
        if ($atm == null) {
            return response_json(400, 'failed', 'ATM not found');
        }

        $amount = $request->amount_withdraw;

        // Return error if customer balance insufficient
        if ($customer->balance < $amount) {
            return response_json(400, 'failed', 'Customer balance is insufficient. Remaining balance is Rp. ' . number_format($customer->balance, 0, ',', '.'));
        }

        $casettes= $atm->casette;
        $data = $this->getFractions($amount, $casettes);
        $fractions = $data['casette'];
        $withdraw = $data['amount'];
        
        // Return error if atm ran out of money
        if ($withdraw != 0) {
            return response_json(400, 'failed', 'ATM ran out of money');
        }

        $update_balance = $customer->update([
            'balance' => $customer->balance - $amount,
        ]);

        $update_casette = $casettes->update([
            'casette_1' => $casettes->casette_1 - $fractions['casette_1'],
            'casette_2' => $casettes->casette_2 - $fractions['casette_2'],
            'casette_3' => $casettes->casette_3 - $fractions['casette_3'],
            'casette_4' => $casettes->casette_4 - $fractions['casette_4'],
            'casette_5' => $casettes->casette_5 - $fractions['casette_5'],
            'casette_6' => $casettes->casette_6 - $fractions['casette_6'],
            'casette_7' => $casettes->casette_7 - $fractions['casette_7'],
        ]);
        
        // Return error if failed to update customer balance or casette
        if (!$update_casette || !$update_balance) {
            return response_json(400, 'failed', 'Error for create new transaction');
        }

        $history_header = HistoryHeader::create([
            'customer_id' => $customer->id,
            'atm_id' => $atm->id,
            'total_withdraw' => $amount,
        ]);

        // Return error if failed to create history
        if (!$history_header) {
            return response_json(400, 'failed', 'Error for create history transaction');
        }

        $history_detail = HistoryDetail::create([
            'history_header_id' => $history_header->id,
            'casette_1' => $fractions['casette_1'],
            'casette_2' => $fractions['casette_2'],
            'casette_3' => $fractions['casette_3'],
            'casette_4' => $fractions['casette_4'],
            'casette_5' => $fractions['casette_5'],
            'casette_6' => $fractions['casette_6'],
            'casette_7' => $fractions['casette_7'],
        ]);

        $result = new stdClass;
        $result = [
            'customer' => $customer,
            'history' => $history_header,
            'detail' => $history_detail,
        ];

        return response_json(200, 'success', $result);
    }

    protected function getFractions($amount, $casettes) {
        $casette = [];
        if ($amount >= 100000) {
            if ($amount % 100000 == 0) {
                if($casettes->casette_1 >= ($amount / 100000)) {
                    $casette['casette_1'] = $amount / 100000;
                    $amount = 0;
                } else {
                    $casette['casette_1'] = $casettes->casette_1;
                    $amount -= (100000 * $casette['casette_1']);
                }

                $casette['casette_2'] = 0;
                $casette['casette_3'] = 0;
                $casette['casette_4'] = 0;
                $casette['casette_5'] = 0;
                $casette['casette_6'] = 0;
                $casette['casette_7'] = 0;
            } else {
                if($casettes->casette_1 >= ($amount / 100000)) {
                    $casette['casette_1'] = floor($amount / 100000);
                    $amount = $amount % 100000;
                } else {
                    $casette['casette_1'] = $casettes->casette_1;
                    $amount -= (100000 * $casette['casette_1']);
                }
            }
        } else {
            $casette['casette_1'] = 0;
        }

        if ($amount >= 50000) {
            if ($amount % 50000 == 0) {
                if($casettes->casette_2 >= ($amount / 50000)) {
                    $casette['casette_2'] = $amount / 50000;
                    $amount = 0;
                } else {
                    $casette['casette_2'] = $casettes->casette_2;
                    $amount -= (50000 * $casette['casette_2']);
                }
                
                $casette['casette_3'] = 0;
                $casette['casette_4'] = 0;
                $casette['casette_5'] = 0;
                $casette['casette_6'] = 0;
                $casette['casette_7'] = 0;
            } else {
                if($casettes->casette_2 >= ($amount / 50000)) {
                    $casette['casette_2'] = floor($amount / 50000);
                    $amount = $amount % 50000;
                } else {
                    $casette['casette_2'] = $casettes->casette_2;
                    $amount -= (50000 * $casette['casette_2']);
                }
            }
        } else {
            $casette['casette_2'] = 0;
        }

        if ($amount >= 20000) {
            if ($amount % 20000 == 0) {
                if($casettes->casette_3 >= ($amount / 20000)) {
                    $casette['casette_3'] = $amount / 20000;
                    $amount = 0;
                } else {
                    $casette['casette_3'] = $casettes->casette_3;
                    $amount -= (20000 * $casette['casette_3']);
                }
                
                $casette['casette_4'] = 0;
                $casette['casette_5'] = 0;
                $casette['casette_6'] = 0;
                $casette['casette_7'] = 0;
            } else {
                if($casettes->casette_3 >= ($amount / 20000)) {
                    $casette['casette_3'] = floor($amount / 20000);
                    $amount = $amount % 20000;
                } else {
                    $casette['casette_3'] = $casettes->casette_3;
                    $amount -= (20000 * $casette['casette_3']);
                }
            }
        } else {
            $casette['casette_3'] = 0;
        }

        if ($amount >= 10000) {
            if ($amount % 10000 == 0) {
                if($casettes->casette_4 >= ($amount / 10000)) {
                    $casette['casette_4'] = $amount / 10000;
                    $amount = 0;
                } else {
                    $casette['casette_4'] = $casettes->casette_4;
                    $amount -= (10000 * $casette['casette_4']);
                }
                
                $casette['casette_5'] = 0;
                $casette['casette_6'] = 0;
                $casette['casette_7'] = 0;
            } else {
                if($casettes->casette_4 >= ($amount / 10000)) {
                    $casette['casette_4'] = floor($amount / 10000);
                    $amount = $amount % 10000;
                } else {
                    $casette['casette_4'] = $casettes->casette_4;
                    $amount -= (10000 * $casette['casette_4']);
                }
            }
        } else {
            $casette['casette_4'] = 0;
        }

        if ($amount >= 5000) {
            if ($amount % 5000 == 0) {
                if($casettes->casette_5 >= ($amount / 5000)) {
                    $casette['casette_5'] = $amount / 5000;
                    $amount = 0;
                } else {
                    $casette['casette_5'] = $casettes->casette_5;
                    $amount -= (5000 * $casette['casette_5']);
                }

                $casette['casette_6'] = 0;
                $casette['casette_7'] = 0;
            } else {
                if($casettes->casette_5 >= ($amount / 5000)) {
                    $casette['casette_5'] = floor($amount / 5000);
                    $amount = $amount % 5000;
                } else {
                    $casette['casette_5'] = $casettes->casette_5;
                    $amount -= (5000 * $casette['casette_5']);
                }
            }
        } else {
            $casette['casette_5'] = 0;
        }

        if ($amount >= 2000) {
            if ($amount % 2000 == 0) {
                if($casettes->casette_6 >= ($amount / 2000)) {
                    $casette['casette_6'] = $amount / 2000;
                    $amount = 0;
                } else {
                    $casette['casette_6'] = $casettes->casette_6;
                    $amount -= (2000 * $casette['casette_6']);
                }

                $casette['casette_7'] = 0;
            } else {
                if($casettes->casette_6 >= ($amount / 2000)) {
                    $casette['casette_6'] = floor($amount / 2000);
                    $amount = $amount % 2000;
                } else {
                    $casette['casette_6'] = $casettes->casette_6;
                    $amount -= (2000 * $casette['casette_6']);
                }
            }
        } else {
            $casette['casette_6'] = 0;
        }

        if ($amount >= 1000) {
            if ($amount % 1000 == 0) {
                if($casettes->casette_7 >= ($amount / 1000)) {
                    $casette['casette_7'] = $amount / 1000;
                    $amount = 0;
                } else {
                    $casette['casette_7'] = $casettes->casette_7;
                    $amount -= (1000 * $casette['casette_7']);
                }
            } else {
                if($casettes->casette_7 >= ($amount / 1000)) {
                    $casette['casette_7'] = floor($amount / 1000);
                    $amount = $amount % 1000;
                } else {
                    $casette['casette_7'] = $casettes->casette_7;
                    $amount -= (1000 * $casette['casette_7']);
                }
            }
        } else {
            $casette['casette_7'] = 0;
        }

        return [
            'amount' => $amount,
            'casette' => $casette,
        ];
    }

}
