<template>
  <div class="create-recipe-container">
    <div class="create-recipe-header">
      <h1>Создание нового рецепта</h1>
      <p>Поделитесь своим рецептом с сообществом</p>
    </div>

    <form class="create-recipe-form">
      <div class="form-section">
        <h2>Основная информация</h2>
        
        <div class="form-group">
          <label for="title">Название рецепта</label>
          <input type="text" id="title" placeholder="Например: Паста Карбонара" class="form-input" />
        </div>

        <div class="form-group">
          <label for="category">Категория</label>
          <select id="category" class="form-input">
            <option value="">Выберите категорию</option>
            <option value="breakfast">Завтрак</option>
            <option value="lunch">Обед</option>
            <option value="dinner">Ужин</option>
            <option value="dessert">Десерт</option>
            <option value="snack">Закуска</option>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Описание</label>
          <textarea id="description" placeholder="Опишите ваш рецепт..." class="form-input" rows="4"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="cooking-time">Время приготовления (мин)</label>
            <input type="number" id="cooking-time" placeholder="30" class="form-input" />
          </div>
          <div class="form-group">
            <label for="servings">Количество порций</label>
            <input type="number" id="servings" placeholder="4" class="form-input" />
          </div>
          <div class="form-group">
            <label for="difficulty">Сложность</label>
            <select id="difficulty" class="form-input">
              <option value="easy">Легко</option>
              <option value="medium">Средне</option>
              <option value="hard">Сложно</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2>Ингредиенты</h2>
        
        <div class="ingredients-list">
          <div class="ingredient-item">
            <div class="form-group">
              <input type="text" placeholder="Название ингредиента" class="form-input" />
            </div>
            <div class="form-group">
              <input type="number" placeholder="Количество" class="form-input" />
            </div>
            <div class="form-group">
              <select class="form-input">
                <option value="">Единица измерения</option>
                <option value="g">г</option>
                <option value="kg">кг</option>
                <option value="ml">мл</option>
                <option value="l">л</option>
                <option value="pcs">шт</option>
                <option value="tbsp">ст. л.</option>
                <option value="tsp">ч. л.</option>
                <option value="cup">стакан</option>
                <option value="pinch">щепотка</option>
                <option value="to-taste">по вкусу</option>
              </select>
            </div>
            <button type="button" class="remove-ingredient">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>
        </div>

        <button type="button" class="add-ingredient-button">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Добавить ингредиент
        </button>
      </div>

      <div class="form-section">
        <h2>Шаги приготовления</h2>
        
        <div class="steps-list">
          <div class="step-item">
            <div class="step-number">1</div>
            <div class="form-group">
              <input type="text" placeholder="Название шага" class="form-input" />
            </div>
            <div class="form-group">
              <textarea placeholder="Описание шага..." class="form-input" rows="3"></textarea>
            </div>
            <button type="button" class="remove-step">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>
        </div>

        <button type="button" class="add-step-button">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Добавить шаг
        </button>
      </div>

      <div class="form-section">
        <h2>Фотографии</h2>
        
        <div class="photos-upload">
          <div class="photo-upload-area">
            <input type="file" id="main-photo" class="photo-input" accept="image/*" />
            <label for="main-photo" class="photo-label">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM3 16l4-4 4 4m4-4 4 4M14 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
              </svg>
              <span>Добавить главное фото</span>
            </label>
          </div>

          <div class="additional-photos">
            <div class="photo-upload-area small">
              <input type="file" class="photo-input" accept="image/*" />
              <label class="photo-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM3 16l4-4 4 4m4-4 4 4M14 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                </svg>
                <span>+</span>
              </label>
            </div>
            <div class="photo-upload-area small">
              <input type="file" class="photo-input" accept="image/*" />
              <label class="photo-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM3 16l4-4 4 4m4-4 4 4M14 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                </svg>
                <span>+</span>
              </label>
            </div>
            <div class="photo-upload-area small">
              <input type="file" class="photo-input" accept="image/*" />
              <label class="photo-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM3 16l4-4 4 4m4-4 4 4M14 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                </svg>
                <span>+</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="cancel-button">Отмена</button>
        <button type="submit" class="submit-button">Опубликовать рецепт</button>
      </div>
    </form>
  </div>
</template>

<script setup>
// Здесь будет логика компонента
</script>

<style scoped>
.create-recipe-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.create-recipe-header {
  text-align: center;
  margin-bottom: 40px;
}

.create-recipe-header h1 {
  font-size: 32px;
  font-weight: 700;
  color: #333;
  margin-bottom: 10px;
}

.create-recipe-header p {
  color: #666;
  font-size: 16px;
}

.create-recipe-form {
  background-color: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.form-section {
  margin-bottom: 40px;
}

.form-section h2 {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #4a90e2;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.ingredients-list {
  margin-bottom: 20px;
}

.ingredient-item {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr auto;
  gap: 15px;
  align-items: start;
  margin-bottom: 15px;
}

.remove-ingredient {
  background: none;
  border: none;
  color: #ff4444;
  cursor: pointer;
  padding: 8px;
  transition: color 0.2s ease;
}

.remove-ingredient:hover {
  color: #cc0000;
}

.add-ingredient-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background-color: #f5f5f5;
  border: none;
  border-radius: 8px;
  color: #666;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.add-ingredient-button:hover {
  background-color: #e0e0e0;
}

.steps-list {
  margin-bottom: 20px;
}

.step-item {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 15px;
  align-items: start;
  margin-bottom: 20px;
}

.step-number {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #4a90e2;
  color: white;
  border-radius: 50%;
  font-weight: 600;
  flex-shrink: 0;
}

.remove-step {
  background: none;
  border: none;
  color: #ff4444;
  cursor: pointer;
  padding: 8px;
  transition: color 0.2s ease;
}

.remove-step:hover {
  color: #cc0000;
}

.add-step-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background-color: #f5f5f5;
  border: none;
  border-radius: 8px;
  color: #666;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.add-step-button:hover {
  background-color: #e0e0e0;
}

.photos-upload {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.photo-upload-area {
  position: relative;
  width: 100%;
  height: 200px;
  border: 2px dashed #ddd;
  border-radius: 8px;
  overflow: hidden;
}

.photo-upload-area.small {
  height: 100px;
}

.photo-input {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  z-index: 2;
}

.photo-label {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #666;
  transition: background-color 0.2s ease;
}

.photo-label span {
  margin-top: 10px;
  font-size: 14px;
}

.photo-upload-area:hover .photo-label {
  background-color: #f5f5f5;
}

.additional-photos {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 20px;
  margin-top: 40px;
}

.cancel-button {
  padding: 12px 24px;
  background-color: #f5f5f5;
  border: none;
  border-radius: 8px;
  color: #666;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.cancel-button:hover {
  background-color: #e0e0e0;
}

.submit-button {
  padding: 12px 24px;
  background-color: #4a90e2;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.submit-button:hover {
  background-color: #3a7bc8;
}
</style> 