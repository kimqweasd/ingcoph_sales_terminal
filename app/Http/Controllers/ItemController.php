<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function getSelection()
    {
        $selection = [];

        $items = Item::all();

        foreach ($items as $item){
            $selection[] = (object)[
                'id' => (int)$item->id,
                'model' => $item->model,
                'name' => $item->name,
                'srp' => number_format($item->srp, 2),
                'quantity' => number_format($item->quantity),
            ];
        }

        return response()->json([
            'message' => 'Success.',
            'selection' => $selection
        ]);
    }
}
