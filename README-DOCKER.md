# Запуск проекта Credit System в Docker

## Предварительные требования

- Docker 
- Docker Compose

## Запуск проекта

1. Клонируйте репозиторий

2. Создайте необходимые директории для Docker:
```bash
mkdir -p docker/nginx/conf.d
```

3. Запустите контейнеры:
```bash
docker-compose up -d
```
или используйте конфигурацию с предопределенными параметрами:
```bash
docker-compose -f docker-compose.local.yaml up -d
```

4. После запуска сервисы будут доступны:
   - Symfony API: [http://localhost:8080/api](http://localhost:8080/api)
   - PostgreSQL: localhost:5432 (user: app, password: password, database: credit_app)

## Информация о контейнерах

### Стандартная конфигурация:
- Веб-сервер: `nginx-credits` (контейнер: `credit-system-nginx`)
- PHP-FPM: `php-credits` (контейнер: `credit-system-php`)
- База данных: `db-credits` (контейнер: `credit-system-db`)
- Сеть: `credit-system-network`
- Том данных: `credit-system-db-data`

### Локальная конфигурация:
- Веб-сервер: `nginx-credits` (контейнер: `credit-system-nginx-local`)
- PHP-FPM: `php-credits` (контейнер: `credit-system-php-local`)
- База данных: `db-credits` (контейнер: `credit-system-db-local`)
- Сеть: `credit-system-network-local`
- Том данных: `credit-system-db-data-local`

## Выполнение команд внутри контейнера

### Composer

```bash
docker-compose exec php-credits composer install
```

### Symfony Console

```bash
docker-compose exec php-credits bin/console cache:clear
```

### Тесты API

```bash
docker-compose exec php-credits vendor/bin/codecept run Api
```

## Разработка Unit-тестов

Для создания и запуска unit-тестов:

1. Установите PHPUnit внутри контейнера:
```bash
docker-compose exec php-credits composer require --dev phpunit/phpunit
```

2. Запустите тесты:
```bash
docker-compose exec php-credits vendor/bin/phpunit
```

## Остановка проекта

```bash
docker-compose down
```

Для удаления данных базы данных:
```bash
docker-compose down -v
``` 