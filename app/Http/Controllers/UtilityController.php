<?php

namespace App\Http\Controllers;

use App\Enums\SyncType;
use App\Models\Item;
use App\Models\Promo;
use App\Models\PromoDetail;
use App\Services\Blueprint\SyncServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class UtilityController extends Controller
{
    protected $service;

    public function __construct(SyncServiceInterface $service)
    {
        $this->service = $service;
    }

    public function utility()
    {
        return view('utility.index');
    }

    public function sync()
    {
        if (request()->wantsJson()) {

            return response()->json([
                'data' => $this->service->start(App::make($this->getMorphMapValue(request()->get('module')))->syncSettings()),
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }
    }
}
