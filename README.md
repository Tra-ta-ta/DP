# Данный проект был сделан в качестве дипломной работы
## Запуск проекта

При работающей хост-машине:

1. Открыть любой браузер
2. Написать в поисковой строке https://mineturic.ru

При настройке для локального пользования:

1. Необходимое программное обеспечение:

   - PHP 8 и выше
   - nodejs 22.15.1 и выше
   - Composer

2. Изменить конфигурационный файл `project/backend/.env`
3. В командной строке перейти в каталог project/backend
4. Выполнить команды:
   composer install
   php artisan migrate
   php artisan serve
   php artisan queue:work
   php artisan schedule:work

5. Заполнить базу данных начальной иформацией, импортировав в таблицу roles данный csv файл `project/backend/newDataBaseRole.csv`, импортировав в таблицу type_rooms данный csv файл `project/backend/newDataBaseTypeRoom.csv`

6. Изменить путь к серверной части в файлах `project/frontend/API/api.js`, `project/frontend/echo/echo.js`, `project/frontend/main.js`

7. В командной строке перейти в каталог project/frontend
8. Выполнить команды:
   npm install
   npm run dev

9. Перейти в браузере по адресу: http://localhost:5173.
