<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\PromoDetail;
use App\Providers\RouteServiceProvider;
use App\Traits\SessionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            $this->forgetAndPut('refresh_token', request()->input('refresh_token'));

            \Log::debug(["Session Authentication" => collect(Session::all())->only(['api', 'access_token', 'refresh_token'])->toArray()]);

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
        Session::forget('refresh_token');

        User::query()->truncate();
        Store::query()->truncate();
        Item::query()->truncate();
        PaymentMethod::query()->truncate();
        Promo::query()->truncate();
        DB::table('item_promo')->truncate();
        PromoDetail::query()->truncate();

        \Log::debug(["Logout Session" => collect(Session::all())->only(['api', 'access_token', 'refresh_token'])->toArray()]);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
