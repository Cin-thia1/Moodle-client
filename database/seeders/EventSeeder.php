<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Evènement 1',
                'date' => '2023-05-01 10:00:00',
                'type' => 'utilisateur',
            ],
            [
                'title' => 'Evènement 2',
                'date' => '2023-05-02 14:00:00',
                'type' => 'utilisateur',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }

        $this->command->info('2 évènements ont été ajoutés avec succès.');
    }
}
