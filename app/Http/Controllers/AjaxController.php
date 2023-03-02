<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    public function run(Request $request)
    {
        $id = Auth::user()->id;
        $user =  User::find($id);
        $used = $user->search_credits;;
        $total = $user->credits;
        return json_encode([
            'usedCredits' => $used,
            'totalCredits'  => $total,
        ]);
    }
}
