<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['L', 'P']);
        $firstName = $gender === 'L' ? fake()->firstNameMale() : fake()->firstNameFemale();
        
        return [
            'nisn' => fake()->unique()->numerify('##########'), // 10 digit NISN
            'nama_siswa' => $firstName . ' ' . fake()->lastName(),
            'jenis_kelamin' => $gender,
            'alamat' => fake()->streetAddress(), // Shorter address
            'id_kelas' => null, // Will be set in seeder
            'id_ta' => null, // Will be set in seeder
        ];
    }
}
