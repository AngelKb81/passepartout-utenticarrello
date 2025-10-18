<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorie = ['elettronica', 'abbigliamento', 'casa', 'sport', 'libri'];
        
        return [
            'codice' => fake()->unique()->bothify('PROD-####-????'),
            'nome' => fake()->words(3, true),
            'descrizione' => fake()->paragraph(2),
            'prezzo' => fake()->randomFloat(2, 10, 999),
            'categoria' => fake()->randomElement($categorie),
            'immagine' => 'products/default-product.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}