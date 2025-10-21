<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = collect(['thriller','scifi','classic','rpg','jazz',' seinen','shoujo','indie'])
            ->map(fn($n)=>Tag::firstOrCreate(['name'=>trim($n)]));
        Item::factory(60)->create()->each(function($item) use($tags){
            $item->tags()->sync($tags->random(rand(1,3))->pluck('id'));
        });
    }
}
