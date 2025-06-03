## Технологии

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3, Inertia.js
- **База данных**: MySQL

## Требования

- PHP >= 8.1
- Node.js >= 16
- Composer
- MySQL >= 8.0

## Установка

1. Установите PHP зависимости:
```bash
composer install
```

2. Установите зависимости:
```bash
npm install
```

3. Скопируйте файл конфигурации:
```bash
cp .env.example .env
```

4. Сгенерируйте ключ приложения:
```bash
php artisan key:generate
```

5. Настройте подключение к базе данных в файле .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Выполните миграции и заполните базу данных начальными данными:
```bash
php artisan migrate --seed
```

7. Создайте символическую ссылку для хранения файлов:
```bash
php artisan storage:link
```

8. Запустите сборку фронтенда:
```bash
npm run dev
```

9. Запустите локальный сервер:
```bash
php artisan serve
```

## Структура базы данных

- `users` - Пользователи системы
- `recipes` - Рецепты
- `categories` - Категории рецептов
- `ingredients` - Ингредиенты
- `steps` - Шаги приготовления
- `reviews` - Отзывы к рецептам
- `favorite_recipes` - Избранные рецепты пользователей

## Статусы рецептов

- `pending` - На рассмотрении
- `approved` - Одобрен
- `rejected` - Отклонен

## Роли пользователей

- Гость (неавторизованный пользователь)
- Пользователь
- Администратор

## API Endpoints

### Рецепты
- `GET /api/recipes` - Получить список рецептов
- `GET /api/recipes/{id}` - Получить конкретный рецепт
- `POST /api/recipes` - Создать новый рецепт
- `PUT /api/recipes/{id}` - Обновить рецепт
- `DELETE /api/recipes/{id}` - Удалить рецепт

### Категории
- `GET /api/categories` - Получить список категорий
- `GET /api/categories/{id}/recipes` - Получить рецепты категории

### Отзывы
- `POST /api/recipes/{id}/reviews` - Добавить отзыв
- `DELETE /api/reviews/{id}` - Удалить отзыв