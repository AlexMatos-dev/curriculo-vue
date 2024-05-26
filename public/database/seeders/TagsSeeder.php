<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/tags.json');
        if(!file_exists($path))
            return;
        $tagsArray = json_decode(file_get_contents($path), true);
        $tagObj = new Tag();
        foreach($tagsArray as $data){
            $name = array_key_exists('name', $data) ? $data['name'] : $data['en'];
            if($tagObj::where('tags_name', $name)->first())
                continue;
            $result = Tag::create([
                'tags_name' => $name
            ]);
            if(!$result || Translation::where('en', $data['en'])->first())
                continue;
            Translation::create([
                'en' => $data['en'],
                'pt' => $data['pt'],
                'es' => $data['es'],
                'category' => Translation::CATEGORY_TAG
            ]);
        }
    }
}
