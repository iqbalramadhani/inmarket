<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Services\OYIService;
use Illuminate\Http\Request;
use App\Models\UserBankAccount;
use Auth;

class UserBankAccountController extends Controller
{
    //

    public function create(Request $request) {
        $banks = Bank::all();
        return view('frontend.user.seller.banks.create', [
            'banks' => $banks
        ]);
    }

    public function checkAccountNumber(Request $request): \Illuminate\Http\JsonResponse
    {
        $check = OYIService::accountInquiry(
            "$request->bank_code",
            "$request->account_number"
        );

        if($check->status->code === "000"){
            $response = [
                'valid' => true,
                'bank_code' => $check->bank_code,
                'account_number' => $check->account_number,
                'account_name' => $check->account_name
            ];
        }else{
            $response = [
                'valid' => false
            ];
        }

        return response()->json($response);
    }

    public function edit(Request $request, $id) {
        $detail = UserBankAccount::find($id);
        $banks = Bank::all();
        return view('frontend.user.seller.banks.edit', compact('detail', 'banks'));
    }

    public function store(Request $request) {

        $bank = Bank::where('code', $request->bank_code)->first();
        UserBankAccount::create([
            'user_id' => Auth()->user()->id,
            'account_name'  => $request->account_name,
            'account_number'  => $request->account_number,
            'bank_code'  => $bank->code,
            'bank_name'  => $bank->name,
        ]);

        flash(translate('New Bank account has successfully created'))->success();
        return redirect()->route('shops.index');
    }

    public function update(Request $request, $id) {
        $bank = Bank::where('code', $request->bank_code)->first();
        UserBankAccount::find($id)->update([
            'account_name'  => $request->account_name,
            'account_number'  => $request->account_number,
            'bank_code'  => $bank->code,
            'bank_name'  => $bank->name,
        ]);

        flash(translate('New Bank account has successfully edit'))->success();
        return redirect()->route('shops.index');
    }

    public function destroy($id) {
        UserBankAccount::find($id)->delete();
        flash(translate('Account has successfully deleted'))->success();
        return redirect()->route('shops.index');
    }
}
