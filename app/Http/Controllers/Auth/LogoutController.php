<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    use ApiResponser;

    public function logout() {

        Auth::logout();

        return redirect()->route('login');
    }

    public function logoutAPI(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();
            return $this->success(['Logout realizado com sucesso.']);
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            return $this->error($ex->getMessage(), 500);
        }


    }
}
