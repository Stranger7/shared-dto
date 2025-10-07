<p>
Компилятор DTO-моделей для PHP и TypeScript
</p>

## Установка

Требования:
- PHP 8.3
- Composer

## Создание новой DTO-модели

- В каталоге `source/` создать yaml-файл с описанием DTO-модели. Структура yaml-файла описана ниже.
- Выполнить из корня проекта `php ./scripts/compile.php`.
- В каталоге `dto/` будут созданы DTO модели.
  DTO модели для PHP находятся в каталоге `dto/php`, для TypeScript в `dto/ts`.

## Структура Yaml-файла
См. примеры в папке `source/Example`

Поля:
- parent - DTO-модель, от которой наследуется создаваемая модель.
  Базовые parent-ы:
  - input - для вх.параметров
  - output - для результата запроса
- properties - массив описаний полей модели.
  Поддерживаемые свойства:
  - name: строка
  - type: string | integer | float | double | boolean | array | object
  - typeOf: тип элемента массива или путь к объекту. Для скалярных type это поле не требуется
  - required: true | false
  - comment: строка
  - default: значение

## Тесты
Выполнить из корня проекта `./vendor/bin/phpunit tests`
