<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Section;
use App\Models\Module;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        // Cours avec sections et modules
        $courses = [
            [
                'fullname' => 'Introduction à l\'Informatique',
                'shortname' => 'INFO101',
                'summary' => 'Ce cours couvre les bases de l\'informatique, y compris les systèmes d\'exploitation, les réseaux et la programmation.',
                'numsections' => 3,
                'category_id' => 5,
                'startdate' => now(),
                'enddate' => now()->addMonths(4),
                'teacher_id' => User::role('ROLE_TEACHER')->first()->id, // Utilisation de Spatie
            ],
            [
                'fullname' => 'Mathématiques pour Sciences Sociales',
                'shortname' => 'MATH101',
                'summary' => 'Ce cours propose une introduction aux mathématiques appliquées dans le contexte des sciences sociales.',
                'numsections' => 3,
                'category_id' => 1,
                'startdate' => now(),
                'enddate' => now()->addMonths(4),
                'teacher_id' => User::role('ROLE_TEACHER')->first()->id, // Utilisation de Spatie
            ],
            [
                'fullname' => 'Biologie Cellulaire et Moléculaire',
                'shortname' => 'BIO101',
                'summary' => 'Le cours couvre les concepts fondamentaux de la biologie cellulaire et moléculaire, y compris la génétique et la biologie des cellules.',
                'numsections' => 3,
                'category_id' => 2,
                'startdate' => now(),
                'enddate' => now()->addMonths(4),
                'teacher_id' => User::role('ROLE_TEACHER')->first()->id, // Utilisation de Spatie
            ],
            [
                'fullname' => 'Physique Moderne',
                'shortname' => 'PHYS101',
                'summary' => 'Ce cours traite des bases de la physique moderne, y compris la relativité et la mécanique quantique.',
                'numsections' => 3,
                'category_id' => 3,
                'startdate' => now(),
                'enddate' => now()->addMonths(4),
                'teacher_id' => User::role('ROLE_TEACHER')->first()->id, // Utilisation de Spatie
            ],
            [
                'fullname' => 'Histoire Contemporaine',
                'shortname' => 'HIST101',
                'summary' => 'Introduction à l\'histoire contemporaine, en se concentrant sur les événements majeurs du XXe siècle.',
                'numsections' => 3,
                'category_id' => 4,
                'startdate' => now(),
                'enddate' => now()->addMonths(4),
                'teacher_id' => User::role('ROLE_TEACHER')->first()->id, // Utilisation de Spatie
            ],
        ];

        // Création des cours, sections et modules
        foreach ($courses as $courseData) {
            $course = Course::create($courseData);

            // Création des sections pour chaque cours
            for ($i = 1; $i <= 3; $i++) {
                $section = Section::create([
                    'name' => "Section $i",
                    'course_id' => $course->id,
                ]);

                // Création des modules pour chaque section
                for ($j = 1; $j <= 3; $j++) {
                    Module::create([
                        'name' => "Module $j",
                        'modname' => "Module $j - $i",
                        'modplural' => "Modules $j - $i",
                        'downloadcontent' => true,
                        'file_path' => "path/to/file/$course->shortname/section_$i/module_$j",
                        'section_id' => $section->id,
                    ]);
                }
            }
        }

        $this->command->info('5 cours, 15 sections et 45 modules ont été ajoutés avec succès.');
    }
}
