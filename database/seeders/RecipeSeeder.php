<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $recipes = [
            [
                'name' => 'Макароны по-флотски',
                'category_name' => 'Мясные блюда',
                'description' => 'Классическое блюдо из макарон с мясным фаршем. Простое в приготовлении и очень сытное блюдо, которое любят и взрослые, и дети.',
                'cooking_time' => '30 минут',
                'image' => 'images/recipes/pasta.png',
                'status' => Recipe::STATUS_APPROVED,
                'ingredients' => [
                    ['name' => 'Макароны', 'amount' => '400', 'unit' => 'г'],
                    ['name' => 'Фарш говяжий', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'Лук репчатый', 'amount' => '2', 'unit' => 'шт'],
                    ['name' => 'Масло растительное', 'amount' => '30', 'unit' => 'мл'],
                    ['name' => 'Соль', 'amount' => '1', 'unit' => 'по вкусу'],
                    ['name' => 'Перец', 'amount' => '1', 'unit' => 'по вкусу']
                ],
                'steps' => [
                    ['order' => 1, 'description' => 'Отварить макароны в подсоленной воде до состояния аль денте'],
                    ['order' => 2, 'description' => 'Нарезать лук мелкими кубиками'],
                    ['order' => 3, 'description' => 'Обжарить лук на растительном масле до золотистого цвета'],
                    ['order' => 4, 'description' => 'Добавить фарш и жарить до готовности'],
                    ['order' => 5, 'description' => 'Смешать макароны с фаршем, посолить и поперчить по вкусу']
                ]
            ],
            [
                'name' => 'Борщ',
                'category_name' => 'Супы',
                'description' => 'Традиционный борщ с говядиной и сметаной.',
                'cooking_time' => '2 часа',
                'image' => 'images/recipes/borsch.png',
                'status' => Recipe::STATUS_APPROVED,
                'ingredients' => [
                    ['name' => 'Говядина', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'Свекла', 'amount' => '2', 'unit' => 'шт'],
                    ['name' => 'Капуста', 'amount' => '300', 'unit' => 'г'],
                    ['name' => 'Картофель', 'amount' => '3', 'unit' => 'шт'],
                    ['name' => 'Морковь', 'amount' => '1', 'unit' => 'шт'],
                    ['name' => 'Лук репчатый', 'amount' => '1', 'unit' => 'шт']
                ],
                'steps' => [
                    ['order' => 1, 'description' => 'Сварить мясной бульон'],
                    ['order' => 2, 'description' => 'Нарезать овощи'],
                    ['order' => 3, 'description' => 'Обжарить свеклу и морковь'],
                    ['order' => 4, 'description' => 'Добавить овощи в бульон'],
                    ['order' => 5, 'description' => 'Варить до готовности']
                ]
            ]
        ];

        $user = User::first();

        foreach ($recipes as $recipeData) {
            $category = Category::where('name', $recipeData['category_name'])->first();
            
            if ($user && $category) {
                $recipe = Recipe::create([
                    'name' => $recipeData['name'],
                    'category_id' => $category->id,
                    'description' => $recipeData['description'],
                    'cooking_time' => $recipeData['cooking_time'],
                    'image' => $recipeData['image'],
                    'user_id' => $user->id,
                    'status' => $recipeData['status'],
                    'rating' => 0,
                    'views' => 0
                ]);

                // Добавляем ингредиенты
                foreach ($recipeData['ingredients'] as $ingredient) {
                    $recipe->ingredients()->create($ingredient);
                }

                // Добавляем шаги
                foreach ($recipeData['steps'] as $step) {
                    $recipe->steps()->create($step);
                }
            }
        }
    }
} 