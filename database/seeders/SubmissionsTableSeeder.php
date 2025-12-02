<?php

namespace Database\Seeders;

use App\Models\Submission;
use Illuminate\Database\Seeder;

class SubmissionsTableSeeder extends Seeder
{
    public function run()
    {
        // Création de 3 soumissions
        $submissions = [
            [
                'status' => 'pending',
                'file_path' => 'uploads/submissions/file1.pdf',
                'assignment_id' => 1,
                'student_id' => 1, // Assurez-vous qu'un étudiant avec cet ID existe
            ],
            [
                'status' => 'corrected',
                'file_path' => 'uploads/submissions/file2.pdf',
                'assignment_id' => 2,
                'student_id' => 2, // Assurez-vous qu'un étudiant avec cet ID existe
            ],
            [
                'status' => 'pending',
                'file_path' => 'uploads/submissions/file3.pdf',
                'assignment_id' => 3,
                'student_id' => 3, // Assurez-vous qu'un étudiant avec cet ID existe
            ],
        ];

        foreach ($submissions as $data) {
            Submission::create($data);
        }

        $this->command->info('3 soumissions ont été ajoutées avec succès.');
    }
}
