<?php

namespace App\Notifications;

use App\Models\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecipeRejected extends Notification implements ShouldQueue
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
            ->subject('Ваш рецепт требует доработки')
            ->line('К сожалению, ваш рецепт "' . $this->recipe->name . '" был отклонен модератором.')
            ->line('Причина: ' . $this->recipe->rejection_reason)
            ->line('Вы можете отредактировать рецепт и отправить его повторно.')
            ->action('Редактировать рецепт', route('recipes.edit', $this->recipe->id))
            ->line('Если у вас есть вопросы, пожалуйста, свяжитесь с нами.');
    }

    public function toArray($notifiable): array
    {
        return [
            'recipe_id' => $this->recipe->id,
            'recipe_name' => $this->recipe->name,
            'message' => 'Ваш рецепт "' . $this->recipe->name . '" был отклонен.',
            'rejection_reason' => $this->recipe->rejection_reason,
            'link' => route('recipes.edit', $this->recipe->id)
        ];
    }
} 