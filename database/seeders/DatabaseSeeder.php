<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\ExerciseProgram;
use App\Models\NutritionPlan;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create doctor
        $doctor = User::create([
            'name' => 'Dr. Mehdi Haniballi',
            'email' => 'doctor@haniballi.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        // Create 3 patients
        $patientsData = [
            [
                'user' => [
                    'name' => 'Sarah Martinez',
                    'email' => 'patient1@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'patient',
                ],
                'patient' => [
                    'first_name' => 'Sarah',
                    'last_name' => 'Martinez',
                    'phone' => '+1 (555) 234-5678',
                    'date_of_birth' => '1990-03-15',
                    'gender' => 'female',
                    'height_cm' => 165.0,
                    'weight_kg' => 68.5,
                    'medical_notes' => 'Looking to lose 10kg for upcoming wedding. No chronic conditions.',
                    'allergies' => 'Gluten intolerance',
                    'status' => 'active',
                ],
            ],
            [
                'user' => [
                    'name' => 'Karim Benali',
                    'email' => 'patient2@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'patient',
                ],
                'patient' => [
                    'first_name' => 'Karim',
                    'last_name' => 'Benali',
                    'phone' => '+1 (555) 345-6789',
                    'date_of_birth' => '1985-07-22',
                    'gender' => 'male',
                    'height_cm' => 180.0,
                    'weight_kg' => 82.0,
                    'medical_notes' => 'Professional football player. Needs performance nutrition plan.',
                    'allergies' => 'None known',
                    'status' => 'active',
                ],
            ],
            [
                'user' => [
                    'name' => 'Leila Tazi',
                    'email' => 'patient3@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'patient',
                ],
                'patient' => [
                    'first_name' => 'Leila',
                    'last_name' => 'Tazi',
                    'phone' => '+1 (555) 456-7890',
                    'date_of_birth' => '1978-11-08',
                    'gender' => 'female',
                    'height_cm' => 162.0,
                    'weight_kg' => 75.0,
                    'medical_notes' => 'Type 2 diabetes diagnosed 2021. Needs low-glycemic diet plan.',
                    'allergies' => 'Shellfish allergy',
                    'status' => 'active',
                ],
            ],
        ];

        foreach ($patientsData as $data) {
            $user = User::create($data['user']);
            $patient = Patient::create(array_merge($data['patient'], ['user_id' => $user->id]));

            // Create 2-3 appointments per patient
            Appointment::create([
                'patient_id' => $patient->id,
                'user_id' => $doctor->id,
                'scheduled_at' => now()->addDays(rand(3, 14))->setHour(rand(9, 16))->setMinute(0),
                'duration_minutes' => 30,
                'type' => 'video',
                'status' => 'confirmed',
                'notes' => 'Initial consultation and assessment.',
            ]);

            Appointment::create([
                'patient_id' => $patient->id,
                'user_id' => $doctor->id,
                'scheduled_at' => now()->subDays(rand(7, 30))->setHour(rand(9, 16))->setMinute(0),
                'duration_minutes' => 30,
                'type' => 'video',
                'status' => 'completed',
                'notes' => 'Follow-up session. Progress reviewed.',
            ]);

            Appointment::create([
                'patient_id' => $patient->id,
                'user_id' => $doctor->id,
                'scheduled_at' => now()->addDays(rand(20, 40))->setHour(rand(9, 16))->setMinute(0),
                'duration_minutes' => 45,
                'type' => 'phone',
                'status' => 'pending',
                'notes' => 'Monthly follow-up to review plan adjustments.',
            ]);

            // Create 1-2 nutrition plans per patient
            NutritionPlan::create([
                'patient_id' => $patient->id,
                'user_id' => $doctor->id,
                'title' => 'Personalized Nutrition Plan - '.$patient->first_name,
                'description' => 'A balanced nutrition plan tailored to your health goals and dietary preferences.',
                'daily_meals' => [
                    ['meal' => 'Breakfast', 'description' => 'Oats with berries and nuts', 'calories' => 400],
                    ['meal' => 'Lunch', 'description' => 'Grilled chicken salad with olive oil dressing', 'calories' => 550],
                    ['meal' => 'Dinner', 'description' => 'Salmon with steamed vegetables and quinoa', 'calories' => 600],
                    ['meal' => 'Snack', 'description' => 'Greek yogurt with honey', 'calories' => 150],
                ],
                'daily_calories' => 1700,
                'protein_grams' => 130,
                'carbs_grams' => 180,
                'fat_grams' => 55,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(60),
                'status' => 'active',
            ]);

            // Create 1-2 exercise programs per patient
            ExerciseProgram::create([
                'patient_id' => $patient->id,
                'user_id' => $doctor->id,
                'title' => 'Starter Wellness Program - '.$patient->first_name,
                'description' => 'A gentle progressive exercise program to support your nutrition plan.',
                'category' => 'stretching',
                'difficulty' => 'beginner',
                'duration_minutes' => 30,
                'exercises' => [
                    ['name' => 'Brisk Walking', 'duration' => '10 min', 'reps' => null],
                    ['name' => 'Bodyweight Squats', 'duration' => null, 'reps' => '3x15'],
                    ['name' => 'Plank Hold', 'duration' => '3x30 sec', 'reps' => null],
                    ['name' => 'Stretching Cool-down', 'duration' => '5 min', 'reps' => null],
                ],
                'sessions_per_week' => 3,
                'video_url' => null,
                'status' => 'active',
            ]);
        }
    }
}
