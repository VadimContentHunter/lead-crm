<?php

namespace crm\src\components\SourceManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\_common\adapters\SourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;
use crm\src\components\SourceManagement\_exceptions\SourceManagementException;

class CreateSource
{
    public function __construct(
        private ISourceRepository $sourceRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создаёт новый источник на основе параметров.
     *
     * Проводит валидацию данных, сохраняет источник в репозиторий
     * и возвращает результат операции с объектом Source или ошибкой.
     *
     * @param  string $title Название источника
     * @return ISourceResult Результат операции: успешный с Source или неуспешный с ошибкой.
     *
     * @throws SourceManagementException Если валидация не пройдена или источник не сохранён.
     * @throws \Throwable В случае неожиданных ошибок при сохранении источника.
     */
    public function execute(string $title): ISourceResult
    {
        // Валидация - здесь можно валидировать просто title
        $validationResult = $this->validator->validateArray(['title' => $title]);

        if (!$validationResult->isValid()) {
            return SourceResult::failure(
                new SourceManagementException(implode('; ', $validationResult->getErrors()))
            );
        }

        $source = new Source($title);

        try {
            $sourceId = $this->sourceRepository->save($source);
            if (!is_int($sourceId) || $sourceId <= 0) {
                throw new SourceManagementException('Источник не сохранён');
            }
            $source->id = $sourceId;
            return SourceResult::success($source);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
