<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = Recipe::all();

        $commonIngredients = [
            ['name' => 'Соль', 'amount' => '1-2', 'unit' => 'ч.л.'],
            ['name' => 'Перец черный', 'amount' => '1/2', 'unit' => 'ч.л.'],
            ['name' => 'Масло растительное', 'amount' => '2-3', 'unit' => 'ст.л.'],
            ['name' => 'Вода', 'amount' => '1-2', 'unit' => 'л']
        ];

        $mainIngredients = [
            ['name' => 'Картофель', 'amount' => '500-700', 'unit' => 'г'],
            ['name' => 'Морковь', 'amount' => '200', 'unit' => 'г'],
            ['name' => 'Лук репчатый', 'amount' => '150', 'unit' => 'г'],
            ['name' => 'Мясо говядина', 'amount' => '500', 'unit' => 'г'],
            ['name' => 'Курица', 'amount' => '1', 'unit' => 'шт'],
            ['name' => 'Рис', 'amount' => '200', 'unit' => 'г'],
            ['name' => 'Макароны', 'amount' => '300', 'unit' => 'г'],
            ['name' => 'Помидоры', 'amount' => '300', 'unit' => 'г'],
            ['name' => 'Огурцы', 'amount' => '200', 'unit' => 'г'],
            ['name' => 'Зелень укропа', 'amount' => '1', 'unit' => 'пучок'],
            ['name' => 'Чеснок', 'amount' => '3-4', 'unit' => 'зубчика'],
            ['name' => 'Сметана', 'amount' => '100', 'unit' => 'г'],
            ['name' => 'Майонез', 'amount' => '50', 'unit' => 'г'],
            ['name' => 'Яйца', 'amount' => '2-3', 'unit' => 'шт'],
            ['name' => 'Мука пшеничная', 'amount' => '300', 'unit' => 'г']
        ];

        foreach ($recipes as $recipe) {
            // Добавляем 2-3 общих ингредиента
            $randomCommon = array_rand($commonIngredients, rand(2, 3));
            foreach ((array)$randomCommon as $index) {
                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'name' => $commonIngredients[$index]['name'],
                    'amount' => $commonIngredients[$index]['amount'],
                    'unit' => $commonIngredients[$index]['unit']
                ]);
            }

            // Добавляем 3-5 основных ингредиентов
            $randomMain = array_rand($mainIngredients, rand(3, 5));
            foreach ((array)$randomMain as $index) {
                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'name' => $mainIngredients[$index]['name'],
                    'amount' => $mainIngredients[$index]['amount'],
                    'unit' => $mainIngredients[$index]['unit']
                ]);
            }
        }
    }
} 