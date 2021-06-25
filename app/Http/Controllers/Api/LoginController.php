<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\SessionHandler;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use App\Models\Store;
use App\Models\User;

class LoginController extends Controller
{
    use SessionHandler;

    public function index()
    {
        if (request()->wantsJson()) {

            $this->forgetAndPut('api', request()->input('api'));
            $this->forgetAndPut('access_token', request()->input('access_token'));

            \Log::debug(["Session Authentication" => collect(Session::all())->only(['api', 'access_token'])->toArray()]);

            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }

        return view('auth.login');
    }

    public function logout()
    {
        Session::forget('api');
        Session::forget('access_token');

        User::query()->truncate();
        Store::query()->truncate();

        \Log::debug(["Logout Session" => collect(Session::all())->only(['api', 'access_token'])->toArray()]);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
