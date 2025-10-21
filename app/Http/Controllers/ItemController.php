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

    public function show(Item $item){ return new ItemResource($item->load('tags')); }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title'=>'required|string|max:255',
            'type'=>'required|in:movie,book,game,manga,anime,music',
            'year'=>'nullable|integer|min:1800|max:2100',
            'author'=>'nullable|string|max:255',
            'description'=>'nullable|string',
            'tags'=>'array',
            'tags.*'=>'string',
        ]);
        $item = Item::create(Arr::except($data,'tags'));

        if(!empty($data['tags'])){
            $ids = collect($data['tags'])->map(fn($n)=>Tag::firstOrCreate(['name'=>$n])->id);
            $item->tags()->sync($ids);
        }
        return new ItemResource($item->load('tags'));
    }

    public function update(Request $r, Item $item)
    {
        $data = $r->validate([
            'title'=>'sometimes|required|string|max:255',
            'type'=>'sometimes|required|in:movie,book,game,manga,anime,music',
            'year'=>'nullable|integer|min:1800|max:2100',
            'author'=>'nullable|string|max:255',
            'description'=>'nullable|string',
            'tags'=>'array',
            'tags.*'=>'string',
        ]);
        $item->update(Arr::except($data,'tags'));
        if($r->has('tags')){
            $ids = collect($data['tags'])->map(fn($n)=>Tag::firstOrCreate(['name'=>$n])->id);
            $item->tags()->sync($ids);
        }
        return new ItemResource($item->load('tags'));
    }

    public function destroy(Item $item){ $item->delete(); return response()->noContent(); }

    public function search(Request $r){ return $this->index($r); } // alias pratique
}
