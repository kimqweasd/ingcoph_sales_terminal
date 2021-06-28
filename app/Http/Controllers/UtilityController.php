<?php

namespace App\Http\Controllers;

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

            $this->getMorphMapValue(request()->get('module'))::query()->truncate();

            $data = $this->getMorphMapValue(request()->get('module'))::create(request()->get('data'));

            return response()->json([
                'data' => $data,
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }
    }
}
