<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Resources\ItemResource;

class ItemController extends Controller
{
    public function index(Request $r)
    {
        $q = Item::with('tags')->latest();

        // filtres: ?type=movie&year=1999&tag=thriller&search=matrix&sort=title&dir=asc
        if ($v = $r->query('type')) $q->where('type', $v);
        if ($v = $r->query('year')) $q->where('year', $v);
        if ($v = $r->query('tag'))  $q->whereHas('tags', fn($s)=>$s->where('name',$v));
        if ($v = $r->query('search')) $q->where(fn($w)=>
            $w->where('title','like',"%$v%")
              ->orWhere('author','like',"%$v%")
              ->orWhere('description','like',"%$v%")
        );

        $sort = in_array($r->query('sort'), ['title','year','created_at']) ? $r->query('sort') : 'created_at';
        $dir  = $r->query('dir') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($sort, $dir);

        return ItemResource::collection($q->paginate(12)->withQueryString());
    }

    public function show(Item $item)
    {
        return new ItemResource($item->load('tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:movie,book,game,manga,anime,music',
            'year'        => 'nullable|integer|min:1800|max:2100',
            'author'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'tags'        => 'array',
            'tags.*'      => 'string',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            // nécessite: php artisan storage:link
            $path = $request->file('image')->store('items', 'public');
        }

        $item = Item::create([
            'title'       => $data['title'],
            'type'        => $data['type'],
            'year'        => $data['year'] ?? null,
            'author'      => $data['author'] ?? null,
            'description' => $data['description'] ?? null,
            'image_path'  => $path,
        ]);

        if (!empty($data['tags'])) {
            $ids = collect($data['tags'])
                ->map(fn($n) => Tag::firstOrCreate(['name' => $n])->id);
            $item->tags()->sync($ids);
        }

        return new ItemResource($item->load('tags'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'year' => 'nullable|integer',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'tags' => 'array',
            'tags.*' => 'string',
        ]);

        // supprimer ancienne image si nouvelle
        if ($request->hasFile('image')) {
            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            $item->image_path = $request->file('image')->store('items', 'public');
        }

        $item->update(Arr::only($data, ['title','type','year','author','description']));

        if (!empty($data['tags'])) {
            $ids = collect($data['tags'])->map(fn($n) => Tag::firstOrCreate(['name' => $n])->id);
            $item->tags()->sync($ids);
        }

        return new ItemResource($item->load('tags'));
    }

    public function destroy(Item $item)
    {
        if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();
        return response()->json(['message' => 'Item deleted']);
    }

    public function search(Request $r){ return $this->index($r); } // alias pratique
}
