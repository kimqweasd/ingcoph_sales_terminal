<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UtilityController extends Controller
{
    public function sync()
    {
        if (request()->wantsJson()) {

            $data = $this->getMorphMapValue(request()->get('module'))::create(request()->get('data'));

            return response()->json([
                'data' => $data,
                'message' => Response::$statusTexts[Response::HTTP_OK]
            ]);
        }

        return view('utility.sync.index');
    }
}
