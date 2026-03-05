<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Create Static Users
        
        // 1.1 Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '9800000001',
                'address' => 'Kathmandu, Nepal',
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 1.2 Staff
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'phone' => '9800000002',
                'address' => 'Lalitpur, Nepal',
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $staffUser->assignRole('Staff');

        // 1.3 Faculty
        $facultyUser = User::firstOrCreate(
            ['email' => 'faculty@gmail.com'],
            [
                'name' => 'Faculty Teacher',
                'password' => Hash::make('password'),
                'phone' => '9800000003',
                'address' => 'Bhaktapur, Nepal',
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $facultyUser->assignRole('Faculty');

        // 2. Create Bulk Random Users
        $rolesForRandom = ['Admin', 'Faculty', 'Staff', 'Student'];

        User::factory()->count(25)->create([
            'password' => Hash::make('password'),
            'status' => true,
        ])->each(function ($user) use ($rolesForRandom, $faker) {
            
            // Assign a random role
            $randomRole = $rolesForRandom[array_rand($rolesForRandom)];
            $user->assignRole($randomRole);

            // If Role is 'Student', create Student Profile
            if ($randomRole === 'Student') {
                $this->createStudentData($user, $faker);
            }
        });

        $this->command->info('Users seeded and roles assigned.');
    }

    /**
     * Helper to create associated student data
     */
    private function createStudentData(User $user, $faker)
    {
        $validStatuses = ['enrolled', 'graduated', 'suspended', 'alumni'];

        $student = Student::create([
            'user_id' => $user->id,
            'registration_number' => 'REG-' . $faker->unique()->numberBetween(10000, 99999),
            'dob' => $faker->date('Y-m-d', '-18 years'),
            'bio' => $faker->sentence,
            'emergency_contact' => $faker->phoneNumber,
            'permanent_address' => $faker->address,
            'academic_status' => $faker->randomElement($validStatuses),
        ]);

        // Education
        foreach (range(1, rand(1, 2)) as $i) {
            $student->education()->create([
                'degree' => $i === 1 ? 'High School' : 'Bachelor',
                'institution' => $faker->company . ' University',
                'passing_year' => $faker->year,
                'grade_or_percentage' => $faker->randomFloat(2, 2.0, 4.0) . ' GPA',
            ]);
        }

        // Skills
        $skills = ['PHP', 'Laravel', 'React', 'Communication', 'Python', 'Design'];
        foreach (range(1, rand(2, 4)) as $i) {
            $student->skills()->create([
                'skill_name' => $skills[array_rand($skills)],
                'proficiency' => $faker->randomElement(['Beginner', 'Intermediate', 'Expert']),
            ]);
        }

        // Guardian
        $student->guardians()->create([
            'name' => $faker->name,
            'relationship' => $faker->randomElement(['Father', 'Mother', 'Uncle']),
            'phone' => $faker->phoneNumber,
            'email' => $faker->safeEmail,
        ]);
    }
}