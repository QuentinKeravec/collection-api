<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $r)
    {
        $items = $r->user()->load(['favorites.tags'])->favorites()->paginate(12);
        return ItemResource::collection($items);
    }
    public function store(Request $r, Item $item)
    {
        $r->user()->favorites()->syncWithoutDetaching([$item->id]);
        return response()->json(['ok'=>true]);
    }
    public function destroy(Request $r, Item $item)
    {
        $r->user()->favorites()->detach($item->id);
        return response()->noContent();
    }
}
