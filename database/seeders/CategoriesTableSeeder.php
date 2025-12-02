<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // Liste des catégories
        $categories = [
            'Mathématiques',
            'Français',
            'Culture Générale',
            'Sport',
            'Informatique'
        ];

        // Création des catégories
        foreach ($categories as $category) {
            Category::create([
                'name' => $category
            ]);
        }

        $this->command->info('5 catégories ont été ajoutées avec succès.');
    }
}
