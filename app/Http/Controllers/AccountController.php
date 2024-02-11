<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->accounts;
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string',
            'password' => 'required|string',
        ]);

        $account = new Account([
            'account_name' => $request->account_name,
            'website_url' => $request->website_url,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'note' => $request->note,
        ]);

        Auth::user()->accounts()->save($account);

        $account->password = $request->password;

        return response()->json($account, 201);
    }

    public function show($id)
    {
        $account = Auth::user()->accounts()->findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $account = Auth::user()->accounts()->findOrFail($id);

        $account->update($request->only(['account_name', 'website_url', 'username', 'note']));

        if ($request->has('password')){
            $account->password = bcrypt($request->password);
            $account->save();
            $account->password = $request->password;
        }

        return response()->json($account);
    }

    public function destroy($id)
    {
        $account = Auth::user()->accounts()->findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}

