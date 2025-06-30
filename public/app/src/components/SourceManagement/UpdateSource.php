<?php

namespace crm\src\components\SourceManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\_common\adapters\SourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;
use crm\src\components\SourceManagement\_exceptions\SourceManagementException;

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
                new SourceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $updatedId = $this->sourceRepository->update($source);

            if ($updatedId === null || $updatedId <= 0) {
                return SourceResult::failure(
                    new SourceManagementException('Не удалось обновить источник')
                );
            }

            return SourceResult::success($source);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
