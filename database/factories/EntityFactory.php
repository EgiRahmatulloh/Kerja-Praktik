<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity>
 */
class EntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'kodeSurat' => $this->faker->randomNumber(3),
            'noSurat' => $this->faker->unique()->randomNumber(5),
            'name' => $this->faker->name(),
            'nik' => $this->faker->unique()->numerify('################'),
            'tempatTglLahir' => $this->faker->city() . ', ' . $this->faker->date('d-m-Y'),
            'pekerjaan' => $this->faker->jobTitle(),
            'address' => $this->faker->address(),
            'keterangan' => $this->faker->paragraph(),
            'tglSurat' => $this->faker->date(),
            'ttd' => $this->faker->randomElement(['Keuchik', 'Sekdes']),
            'namaTtd' => $this->faker->name(),
            'type' => $this->faker->randomElement(['usaha', 'domisili']),
        ];
    }
}
