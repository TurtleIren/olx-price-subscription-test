# olx-price-subscription-test

Необхідно реалізувати сервіс, що дає змогу стежити за зміною ціни оголошення на OLX:

1. Сервіс повинен надати HTTP метод для підписки на зміну ціни. На вхід метод отримує - посилання на оголошення, email на який надсилати повідомлення.

2. Після успішної підписки, сервіс повинен стежити за ціною оголошення і надсилати повідомлення на вказаний email.

3. Якщо кілька користувачів підписалися на одне й те саме оголошення, сервіс не повинен зайвий раз перевіряти ціну оголошення.

Результати роботи мають включати:

* Cхему/діаграму роботи сервісу та короткий його опис
* Посилання на репозиторій з кодом
* Підписка на зміну ціни
* Відстеження змін ціни
* Надсилання повідомлення на пошту
* Мова програмування - PHP

Якщо в ході завдання зʼявилось декілька варіантів реалізації, то опишіть переваги і недоліки кожного з них. Вкажіть чому саме обрали той чи інший варіант.

Щоб отримати ціну оголошення, можна:
* парсити web-сторінку оголошення
* самостійно проаналізувати трафік на мобільних додатках або мобільному сайті і з'ясувати який там API для отримання інформації про оголошення

Ускладнення:

* Реалізувати повноцінний сервіс, який розв'язує поставлене завдання (сервіс має запускатися в docker-контейнері).
* Написані тести (постарайтеся досягти покриття в 70% і більше).
* Підтвердження email користувача.

##Тестування захищених роутів у Swagger

Крок 1: Отримання API токена для тестування
Отримайте API токен для користувача, виконавши запит на авторизацію (наприклад, через /api/login).

Використовуйте отриманий токен для тестування захищених маршрутів у Swagger.

Крок 2: Додавання токена до Swagger UI
Відкрийте Swagger UI та натисніть на кнопку Authorize.

Введіть токен у полі Authorization у форматі Bearer <your-token> і натисніть Authorize.

Після авторизації у Swagger UI ви зможете тестувати захищені маршрути, і ваші запити будуть включати токен авторизації у заголовку.
