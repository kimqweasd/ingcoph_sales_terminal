<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UtilityController extends Controller
{

    public function utility()
    {
        return view('utility.index');
    }

    public function sync()
    {
        if (request()->wantsJson()) {

            \Log::debug(request()->all());

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
                    if ((int)request()->get('page') === 1) {
                        $this->getMorphMapValue(request()->get('module'))::query()->truncate();
                    }

                    $this->getMorphMapValue(request()->get('module'))::insert(request()->get('data'));

                    $data = ['count' => $this->getMorphMapValue(request()->get('module'))::count()];
                    break;
            }

            return response()->json([
                'data' => $data,
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }
    }
}
