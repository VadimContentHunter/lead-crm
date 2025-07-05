<?php

namespace crm\src\services;

use InvalidArgumentException;

/**
 * Универсальный валидатор для объектов (Data Object).
 *
 * Позволяет добавлять правила валидации для конкретных свойств объекта
 * и проверять эти свойства по заданным правилам.
 *
 * Пример использования:
 *
 * ```php
 * $validator = new Validator();
 *
 * // Добавляем правило для поля "login"
 * $validator->addRule('login', function ($value) {
 *     if (empty(trim($value))) {
 *         return 'Login не может быть пустым';
 *     }
 *     if (strlen($value) < 3) {
 *         return 'Login должен быть не менее 3 символов';
 *     }
 *     return null;
 * });
 *
 * // Добавляем правило для поля "plainPassword"
 * $validator->addRule('plainPassword', function ($value) {
 *     if (empty($value)) {
 *         return 'Пароль не может быть пустым';
 *     }
 *     if (strlen($value) < 8) {
 *         return 'Пароль должен содержать минимум 8 символов';
 *     }
 *     return null;
 * });
 *
 * // Объект для валидации (например, DTO)
 * $UserInputDto = new class {
 *     public string $login = 'john';
 *     public string $plainPassword = 'secret123';
 * };
 *
 * try {
 *     $validator->validate($UserInputDto);
 *     echo "Валидация прошла успешно";
 * } catch (InvalidArgumentException $e) {
 *     echo "Ошибка валидации: " . $e->getMessage();
 * }
 * ```
 */

class Validator
{
    /**
     * Массив правил валидации.
     * Ключ — имя свойства объекта.
     * Значение — callback-функция, принимающая значение свойства и
     * возвращающая строку с сообщением об ошибке или null при отсутствии ошибок.
     *
     * @var array<string, callable(mixed): (string|null)>
     */
    private array $rules = [];

    /**
     * Добавляет правило валидации для указанного поля.
     *
     * @param string   $field Имя свойства объекта, к которому применяется правило.
     * @param callable $rule  Правило валидации: функция, принимающая значение поля и
     *                        возвращающая строку с сообщением об ошибке или null, если ошибок нет.
     *
     * @return void
     */
    public function addRule(string $field, callable $rule): void
    {
        $this->rules[$field] = $rule;
    }

    /**
     * Выполняет валидацию объекта по всем добавленным правилам.
     *
     * @param object $dataObj      Объект (например, Data Object)
     *                             для валидации.
     * @param string[] $ignoreFields Массив имён полей, которые нужно пропустить при валидации.
     *
     * @throws InvalidArgumentException Если в объекте отсутствует поле,
     *                                  для которого задано правило,
     *                                  или если значение поля не проходит проверку.
     *
     * @return void
     */
    public function validate(object $dataObj, array $ignoreFields = []): void
    {
        foreach ($this->rules as $field => $rule) {
            if (in_array($field, $ignoreFields, true)) {
                continue; // пропускаем поле
            }

            if (!property_exists($dataObj, $field)) {
                throw new InvalidArgumentException("Field '$field' does not exist in Data Object");
            }

            $value = $dataObj->$field;
            $errorMessage = $rule($value);

            if (is_string($errorMessage)) {
                throw new InvalidArgumentException($errorMessage);
            }
        }
    }


    /**
     * Выполняет валидацию массива по всем добавленным правилам.
     *
     * @param array<string,mixed> $dataArray    Массив данных
     *                                          для валидации.
     * @param string[] $ignoreFields Массив имён полей, которые
     *                               нужно пропустить при валидации.
     *
     * @throws InvalidArgumentException Если в массиве отсутствует поле,
     *                                  для которого задано правило,
     *                                  или если значение поля не проходит проверку.
     *
     * @return void
     */
    public function validateArray(array $dataArray, array $ignoreFields = []): void
    {
        foreach ($this->rules as $field => $rule) {
            if (in_array($field, $ignoreFields, true)) {
                continue; // пропускаем поле
            }

            if (!array_key_exists($field, $dataArray)) {
                throw new InvalidArgumentException("Field '$field' does not exist in data array");
            }

            $value = $dataArray[$field];
            $errorMessage = $rule($value);

            if (is_string($errorMessage)) {
                throw new InvalidArgumentException($errorMessage);
            }
        }
    }
}
