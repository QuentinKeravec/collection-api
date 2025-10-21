<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(){ return TagResource::collection(Tag::orderBy('name')->get()); }

    public function store(Request $r){
        $data = $r->validate(['name'=>'required|string|max:100']);
        return new TagResource(Tag::firstOrCreate(['name'=>$data['name']]));
    }
}
