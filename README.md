# Demo Marketplace

**Backend:** Laravel 12, PostgreSQL, Meilisearch, Mailpit    
**Frontend:** Vue 3, Inertia.js 2, Tailwind CSS 4, ShadCN/Vue, JavaScript ES6+    
**Инфраструктура:** Docker, Laravel Sail, Makefile

### Пакеты

- `spatie/laravel-data` для DTO объектов и создания прослойки перед передачей на фронт (аналог дефолтных ресурсов, в проекте Data объекты)
- `spatie/laravel-medialibrary` для работы с изображениями (обратка + создание вариаций)
- `kalnoy/nestedset` для вложенности категорий
- `webmozart/assert` для простой проверки значений
- `mews/purifier` для обработки HTML в редакторе текста
- `diglactic/laravel-breadcrumbs` для создания хлебных крошек
- `inertiaui/modal` для работы с модальными окнами (инициализация как у страниц инерции)

### Запуск

```bash  
git clone https://github.com/mozg404/demo-marketplace.git
cd demo-marketplace
composer i
make init
```

После установки запустить в **отдельных терминалах:**  

```bash  
# Терминал 1: Очереди  
make queue  
  
# Терминал 2: Планировщик  
make schedule  
  
# Терминал 3: Фронт  
make frontend  
```  

Основная часть данных генерируется именно в очереди, однако для просмотра достаточно и того, что создаст сидер на горячую, поэтому минимальный набор команд будет:

```bash
composer i
make init
make frontend
```  
  
#### Ссылки  
  
**Сайт:** http://localhost:8080  
**Админка:** http://localhost:8080/admin  
**Telescope:** http://localhost:8080/telescope  
**Mailpit:** http://localhost:8025  
**Meilisearch:** http://localhost:7700  
  
#### Данные для авторизации  
  
**Логин:** user@gmail.com  
**Пароль:** 12345678