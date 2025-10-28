<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateur de démo
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'), // login de démo
        ]);

        $tags = collect(['thriller','scifi','classic','rpg','jazz','seinen','shoujo','indie','action epic','drame','aventure','comedie'])
            ->map(fn($n)=>Tag::firstOrCreate(['name'=>trim($n)]));

        // Set fixe d'items pour la démo
        $defs = [
            ['Fullmetal Alchemist Brotherhood', 'FullmetalAlchemistBrotherhood.png', 'anime', 2009, 'Hiromu Arakawa', 'Deux frères en quête d\'une Pierre du Philosophe suite à une tentative de faire revivre leur mère décédée a mal tourné et les laisse dans des formes physiques endommagées.', ['action epic','drame','aventure']],
            ['One Piece', 'OnePiece.png', 'anime', 1999, 'Eiichirô Oda', 'Les aventures de Monkey D. Luffy et de ses amis afin de trouver le plus grand trésor jamais, laissé par le légendaire Pirate, Gol D Roger, le fameux trésor nommé "One Piece".', ['aventure','action epic','comedie']],
            ['Breaking Bad', 'BreakingBad.png', 'serie', 2008, 'Vince Gilligan', 'Un professeur de chimie de lycée chez qui on a diagnostiqué un cancer du poumon inopérable se tourne vers la fabrication et la vente de méthamphétamine pour assurer l\'avenir de sa famille.', ['thriller','drame']],
            ['Game of Thrones', 'GameofThrones.png', 'serie', 2011, 'David Benioff/D.B. Weiss', 'Neuf familles nobles se battent pour le contrôle des terres mythiques de Westeros, tandis qu\'un ancien ennemi revient après avoir été endormi pendant des milliers d\'années.', ['drame','action epic']],
            ['Friends', 'Friends.png', 'serie', 1994, 'David Crane/Marta Kauffman', 'Suit les vies personnelles et professionnelles de six amis d\'une vingtaine et trentaine d\'années vivant à Manhattan.', ['comedie','classic']],
            ['Shinkegi No Kyojin', 'ShinkegiNoKyojin.jpg', 'manga', 2009, 'Hajime Isayama', 'Après la destruction de sa ville natale et la mort de sa mère, le jeune Eren Yeager promet de purger la terre des géants humanoïdes appelés Titans qui menacent l\'humanité toute entière.', ['seinen','action epic','drame']],
            ['Pulp Fiction', 'PulpFiction.png', 'film', 1994, 'Quentin Tarantino', 'Les vies de deux hommes de main, d\'un boxeur, de la femme d\'un gangster et de deux braqueurs s\'entremêlent dans quatre histoires de violence et de rédemption.', ['thriller','classic']],
        ];

        foreach ($defs as [$title, $imageFilename, $type, $year, $author, $description, $tagNames]) {

            $storedImagePath = $this->putDemoImageOrNull($imageFilename);

            $item = Item::create([
                'title' => $title,
                'image_path' => $storedImagePath,
                'type' => $type,
                'year' => $year,
                'author' => $author,
                'description' => $description,
            ]);

            $tagIds = $tags->whereIn('name', $tagNames)->pluck('id');
            $item->tags()->sync($tagIds);

            // 40 %
            if (rand(1, 100) <= 40) {
                $user->favorites()->attach($item->id);
            }
        }
    }

    private function putDemoImageOrNull(string $filename): ?string
    {
        $src = database_path('seeders/images/'.$filename);

        if (file_exists($src)) {
            Storage::disk('public')->putFileAs('items', new File($src), $filename);
            return 'items/'.$filename;
        }

        return null;
    }
}
