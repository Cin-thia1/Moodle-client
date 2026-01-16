<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    public function run()
    {
        // Liste des questions à insérer
        $questions = [
            'Quel est le résultat de 2 + 2 ?',
            'Quel est le carré de 5 ?',
            'Combien de côtés a un triangle ?',
            'Quel est le produit de 7 x 6 ?',
            'Quelle est la racine carrée de 9 ?',
            'Qui a écrit "Les Misérables" ?',
            'Comment s’appelle le héros du livre "Le Petit Prince" ?',
            'Quel est le nom de la capitale du Japon ?',
            'Quel est le plus grand désert du monde ?',
            'Quelle est la définition du mot "amour" ?',
            'Quel sport se joue avec une balle orange ?',
            'Qui a gagné la Coupe du Monde de football en 2018 ?',
            'Quel joueur de tennis a remporté le plus de Grand Slams ?',
            'Quel est le sport national du Japon ?',
            'Quel est le plus grand océan du monde ?',
            'En quelle année l’homme a-t-il marché sur la Lune ?',
            'Qui a écrit "Le Rouge et le Noir" ?',
            'Que signifie l’expression "avoir le cœur sur la main" ?',
            'Quel est le plus grand stade de football en Europe ?',
            'Quel est le plus grand pays du monde par superficie ?'
        ];

        // Insertion des questions dans la table
        foreach ($questions as $index => $content) {
            Question::create([
                'content' => $content,
                'choices' => json_encode(['Option A', 'Option B', 'Option C', 'Option D']),
                'correct_choice_id' => rand(0, 3),
                
            ]);
        }

        $this->command->info('20 questions ont été ajoutées avec succès.');
    }
}
