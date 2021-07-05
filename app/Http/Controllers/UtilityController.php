<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Promo;
use App\Models\PromoDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UtilityController extends Controller
{

    public function utility()
    {
        return view('utility.index');
    }

    public function sync()
    {
        if (request()->wantsJson()) {

            //\Log::debug(request()->all());

            $data = null;

            switch(request()->get('module')){
                case 'user':
                    $this->getMorphMapValue(request()->get('module'))::query()->truncate();

                    $data = $this->getMorphMapValue(request()->get('module'))::create(request()->get('data'));
                    break;

                case 'store':
                    $this->getMorphMapValue(request()->get('module'))::query()->truncate();

                    Item::query()->truncate();

                    $data = $this->getMorphMapValue(request()->get('module'))::create(request()->get('data'));
                    break;

                case 'items':
                case 'payment_methods':
                    if ((int)request()->get('page') === 1) {
                        $this->getMorphMapValue(request()->get('module'))::query()->truncate();
                    }

                    $this->getMorphMapValue(request()->get('module'))::insert(request()->get('data'));

                    $data = ['count' => $this->getMorphMapValue(request()->get('module'))::count()];
                    break;

                case 'promos':
                    if ((int)request()->get('page') === 1) {
                        Promo::query()->truncate();
                        DB::table('item_promo')->truncate();
                        PromoDetail::query()->truncate();
                    }

                    foreach (request()->get('data') as $data) {

                        Promo::create($data['promo']);

                        if (isset($data['details']) && !empty($data['details'])) {
                            foreach ($data['details'] as $detail) {
                                PromoDetail::create($detail);
                            }
                        }

                        if (isset($data['pivot']) && !empty($data['pivot'])) {
                            foreach ($data['pivot'] as $pivot) {
                                DB::table('item_promo')->insert([
                                    'item_id' => (int)$pivot['item_id'],
                                    'promo_id' => (int)$pivot['promo_id'],
                                    'start_date' => Carbon::parse($pivot['start_date'])->startOfDay()->toDateTimeString(),
                                    'end_date' => Carbon::parse($pivot['end_date'])->endOfDay()->toDateTimeString(),
                                ]);
                            }
                        }
                    }

                    $data = ['count' => Promo::count()];
                    break;
            }

            return response()->json([
                'data' => $data,
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }
    }
}
