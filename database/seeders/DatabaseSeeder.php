<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Demo data. Every faculty account gets the password "123" — change it after first login. */
    public function run(): void
    {
        foreach ([
            ['anika', 'Anika Akter'],
            ['kawsir', 'Kawser'],
            ['najma', 'Najma Akter'],
            ['saima', 'Saima Akter'],
            ['shanchayan', 'Sanchayan Bhattacharjee'],
            ['manooj', 'Manooj Hazarika'],
            ['joy', 'SS Joy'],
        ] as [$userName, $name]) {
            Faculty::firstOrCreate(['user_name' => $userName], ['name' => $name, 'password' => '123']);
        }

        foreach ([['CSE-192', 'SS Joy'], ['CSE-193', 'Sani']] as [$roll, $name]) {
            Student::firstOrCreate(['roll_no' => $roll], ['name' => $name]);
        }

        foreach ([[2023, 'SPRING SEMESTER'], [2023, 'AUTUMN SEMESTER']] as [$year, $term]) {
            AcademicSession::firstOrCreate(['year' => $year, 'term' => $term]);
        }

        foreach ([
            ['CS1', 'Software Engineering', 2],
            ['CS2', 'Embedded Management System', 3],
            ['CS3', 'Computer Networking', 3],
            ['CS4', 'Artificial Intelligence', 4],
            ['CS5', 'Theory of Computation', 3],
            ['CS6', 'Demystifying Networking', 1],
        ] as [$code, $title, $credit]) {
            Course::firstOrCreate(['code' => $code], ['title' => $title, 'credit' => $credit]);
        }
    }
}
