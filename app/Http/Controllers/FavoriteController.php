<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with('tags')
            ->latest()
            ->get();

        return ItemResource::collection($favorites);
    }

    public function store(Request $request, Item $item)
    {
        $user = $request->user();

        if (! $user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->attach($item->id);
        }

        return response()->json(['message' => 'Ajouté aux favoris']);
    }

    public function destroy(Request $request, Item $item)
    {
        $user = $request->user();

        $user->favorites()->detach($item->id);

        return response()->json(['message' => 'Retiré des favoris']);
    }
}
