<?php

namespace crm\src\components\SourcesManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourcesManagement\_entities\Source;
use crm\src\components\SourcesManagement\_common\adapters\SourceResult;
use crm\src\components\SourcesManagement\_common\interfaces\ISourceResult;
use crm\src\components\SourcesManagement\_common\interfaces\ISourceRepository;
use crm\src\components\SourcesManagement\_exceptions\SourcesManagementException;

class UpdateSource
{
    public function __construct(
        private ISourceRepository $sourceRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Обновляет источник на основе объекта Source.
     *
     * @param  Source $source Объект Source с обновлёнными данными (id обязателен).
     * @return ISourceResult Результат операции: успешный с обновлённым Source или с ошибкой.
     */
    public function execute(Source $source): ISourceResult
    {
        $validationResult = $this->validator->validate($source);

        if (!$validationResult->isValid()) {
            return SourceResult::failure(
                new SourcesManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $updatedId = $this->sourceRepository->update($source);

            if ($updatedId === null || $updatedId <= 0) {
                return SourceResult::failure(
                    new SourcesManagementException('Не удалось обновить источник')
                );
            }

            return SourceResult::success($source);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
