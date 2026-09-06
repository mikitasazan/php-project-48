# Вычислитель отличий (PHP)

[![hexlet-check](https://github.com/mikitasazan/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/mikitasazan/php-project-48/actions/workflows/hexlet-check.yml)
[![main](https://github.com/mikitasazan/php-project-48/actions/workflows/main.yml/badge.svg)](https://github.com/mikitasazan/php-project-48/actions/workflows/main.yml)

Программа сравнивает два файла с настройками и показывает, чем они отличаются:
что появилось, что пропало, что поменяло значение. Понимает JSON и YAML,
вложенность любой глубины.

## Требования

- PHP 8.1 или новее
- Composer, доступный глобально командой `composer`

## Установка

```bash
git clone https://github.com/mikitasazan/php-project-48.git
cd php-project-48
make install
```

## Запуск

```bash
./bin/gendiff first.json second.json
./bin/gendiff --format plain first.yaml second.yaml
./bin/gendiff --format json first.json second.json
./bin/gendiff --help
```

## Форматы вывода

| Формат | Что даёт |
|---|---|
| `stylish` (по умолчанию) | дерево с отметками `+` и `-` у изменившихся строк |
| `plain` | список изменений фразами: что добавлено, удалено, обновлено |
| `json` | то же дерево машиночитаемо, чтобы отдать другой программе |

## Разработка

```bash
make lint           # PSR-12 по bin, src и tests
make test           # тесты PHPUnit
make test-coverage  # тесты и порог покрытия (COVERAGE_MIN, по умолчанию 80)
```

## Записи прохождения

Аскинемы пока нет — её записывает владелец репозитория со своей машины.
