<?php

namespace App\Notifications;

use App\Models\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecipeApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $recipe;

    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ваш рецепт одобрен!')
            ->line('Поздравляем! Ваш рецепт "' . $this->recipe->name . '" был одобрен модератором.')
            ->line('Теперь он доступен всем пользователям сайта.')
            ->action('Посмотреть рецепт', route('recipes.show', $this->recipe->id))
            ->line('Спасибо за ваш вклад в развитие нашего сообщества!');
    }

    public function toArray($notifiable): array
    {
        return [
            'recipe_id' => $this->recipe->id,
            'recipe_name' => $this->recipe->name,
            'message' => 'Ваш рецепт "' . $this->recipe->name . '" был одобрен.',
            'link' => route('recipes.show', $this->recipe->id)
        ];
    }
} 