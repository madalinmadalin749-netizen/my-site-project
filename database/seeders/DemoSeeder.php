<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Question;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::firstOrCreate(
            ['slug' => 'politie'],
            ['name' => 'Poliție']
        );

        Question::create([
            'category_id' => $cat->id,
            'prompt' => 'Capitala României este:',
            'a' => 'București',
            'b' => 'Cluj',
            'c' => 'Iași',
            'd' => 'Timișoara',
            'correct' => 'a',
            'difficulty' => 1,
        ]);
    }
}
