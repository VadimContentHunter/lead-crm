<?php

namespace crm\src\Investments\InvActivity\_common\mappers;

use DateTimeImmutable;
use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_entities\DealDirection;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_entities\TradePair;

class InvActivityMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbInvActivityDto $dto
     * @param  bool $strictPair Использовать строгую проверку пары (default: false)
     * @return InvActivity
     */
    public static function fromDbToEntity(DbInvActivityDto $dto, bool $strictPair = false): InvActivity
    {
        return new InvActivity(
            InvActivityHash: $dto->activity_hash,
            leadUid: $dto->lead_uid,
            type: DealType::from($dto->type),
            openTime: new DateTimeImmutable($dto->open_time),
            closeTime: $dto->close_time ? new DateTimeImmutable($dto->close_time) : null,
            pair: $strictPair ? self::strictPair($dto->pair) : self::normalizePair($dto->pair),
            openPrice: $dto->open_price,
            closePrice: $dto->close_price,
            amount: $dto->amount,
            direction: DealDirection::from($dto->direction),
            result: $dto->result,
            id: $dto->id,
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvActivity $entity
     * @return DbInvActivityDto
     */
    public static function fromEntityToDb(InvActivity $entity): DbInvActivityDto
    {
        return new DbInvActivityDto(
            id: $entity->id,
            activity_hash: $entity->InvActivityHash,
            lead_uid: $entity->leadUid,
            type: $entity->type->value,
            open_time: $entity->openTime?->format('Y-m-d H:i:s') ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            close_time: $entity->closeTime?->format('Y-m-d H:i:s'),
            pair: self::normalizePair($entity->pair),
            open_price: $entity->openPrice,
            close_price: $entity->closePrice,
            amount: $entity->amount,
            direction: $entity->direction->value,
            result: $entity->result,
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InvActivityInputDto $dto
     * @param  bool $strictPair Использовать строгую проверку пары (default: false)
     * @return InvActivity
     */
    public static function fromInputToEntity(InvActivityInputDto $dto, bool $strictPair = false): InvActivity
    {
        return new InvActivity(
            InvActivityHash: $dto->InvActivityHash ?? throw new \InvalidArgumentException('InvActivityHash is required'),
            leadUid: $dto->leadUid ?? throw new \InvalidArgumentException('leadUid is required'),
            type: $dto->type ? DealType::from($dto->type) : DealType::ACTIVE,
            openTime: $dto->openTime ? new DateTimeImmutable($dto->openTime) : new DateTimeImmutable(),
            closeTime: $dto->closeTime ? new DateTimeImmutable($dto->closeTime) : null,
            pair: $dto->pair ? ($strictPair ? self::strictPair($dto->pair) : self::normalizePair($dto->pair)) : '',
            openPrice: $dto->openPrice ?? 0.0,
            closePrice: $dto->closePrice,
            amount: $dto->amount ?? 0.0,
            direction: $dto->direction ? DealDirection::from($dto->direction) : DealDirection::LONG,
            result: $dto->result,
            id: $dto->id,
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvActivityInputDto $dto
     * @param  bool $strictPair Использовать строгую проверку пары (default: false)
     * @return DbInvActivityDto
     */
    public static function fromInputToDb(InvActivityInputDto $dto, bool $strictPair = false): DbInvActivityDto
    {
        $InvActivityHash = $dto->InvActivityHash ?? uniqid('act_', true);
        $leadUid = $dto->leadUid ?? throw new \InvalidArgumentException('leadUid is required');
        $type = $dto->type ?? DealType::ACTIVE->value;
        $direction = $dto->direction ?? DealDirection::LONG->value;

        $openTime = $dto->openTime
            ? (new DateTimeImmutable($dto->openTime))->format('Y-m-d H:i:s')
            : (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $closeTime = $dto->closeTime
            ? (new DateTimeImmutable($dto->closeTime))->format('Y-m-d H:i:s')
            : null;

        $pair = $dto->pair
            ? ($strictPair ? self::strictPair($dto->pair) : self::normalizePair($dto->pair))
            : '';

        return new DbInvActivityDto(
            id: $dto->id,
            activity_hash: $InvActivityHash,
            lead_uid: $leadUid,
            type: $type,
            open_time: $openTime,
            close_time: $closeTime,
            pair: $pair,
            open_price: $dto->openPrice ?? 0.0,
            close_price: $dto->closePrice,
            amount: $dto->amount ?? 0.0,
            direction: $direction,
            result: $dto->result,
        );
    }

    /**
     * Преобразует DTO для БД в ассоциативный массив для сохранения.
     *
     * @param  DbInvActivityDto $dto
     * @param  bool $strictPair Использовать строгую проверку пары (default: false)
     * @return array<string, mixed>
     */
    public static function fromDbToArray(DbInvActivityDto $dto, bool $strictPair = false): array
    {
        $pair = $strictPair ? self::strictPair($dto->pair) : self::normalizePair($dto->pair);

        return [
            'id' => $dto->id,
            'activity_hash' => $dto->activity_hash,
            'lead_uid' => $dto->lead_uid,
            'type' => $dto->type,
            'open_time' => $dto->open_time,
            'close_time' => $dto->close_time,
            'pair' => $pair,
            'open_price' => $dto->open_price,
            'close_price' => $dto->close_price,
            'amount' => $dto->amount,
            'direction' => $dto->direction,
            'result' => $dto->result,
        ];
    }

    /**
     * Преобразует массив данных из БД в DTO.
     *
     * @param  array<string, mixed> $data Ассоциативный массив данных из БД.
     * @return DbInvActivityDto DTO, соответствующий данным из массива.
     */
    public static function fromArrayToDb(array $data): DbInvActivityDto
    {
        return new DbInvActivityDto(
            activity_hash: $data['activity_hash'],
            lead_uid: $data['lead_uid'],
            type: $data['type'],
            open_time: $data['open_time'],
            close_time: $data['close_time'] ?? null,
            pair: $data['pair'],
            open_price: (float) $data['open_price'],
            close_price: isset($data['close_price']) ? (float) $data['close_price'] : null,
            amount: (float) $data['amount'],
            direction: $data['direction'],
            result: isset($data['result']) ? (float) $data['result'] : null,
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    /**
     * Преобразует массив в входной DTO (InvActivityInputDto).
     *
     * @param  array<string, mixed> $data
     * @return InvActivityInputDto
     */
    public static function fromArrayToInput(array $data): InvActivityInputDto
    {
        return new InvActivityInputDto(
            id: isset($data['id']) ? (int) $data['id'] : null,
            InvActivityHash: isset($data['activity_hash']) ? (string) $data['activity_hash'] : null,
            leadUid: isset($data['lead_uid']) ? (string) $data['lead_uid'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            openTime: isset($data['open_time']) ? (string) $data['open_time'] : null,
            closeTime: isset($data['close_time']) ? (string) $data['close_time'] : null,
            pair: isset($data['pair']) ? (string) $data['pair'] : null,
            openPrice: isset($data['open_price']) ? (float) $data['open_price'] : null,
            closePrice: isset($data['close_price']) ? (float) $data['close_price'] : null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            direction: isset($data['direction']) ? (string) $data['direction'] : null,
            result: isset($data['result']) ? (float) $data['result'] : null
        );
    }



    /**
     * Нормализует строку торговой пары (например, BTC-USDT → BTC/USDT).
     *
     * @param  string $input
     * @return string
     */
    public static function normalizePair(string $input): string
    {
        $normalized = strtoupper(trim($input));
        $normalized = str_replace(['-', '_', ' '], '/', $normalized);

        foreach (TradePair::cases() as $case) {
            if ($normalized === strtoupper($case->value)) {
                return $case->value;
            }
        }

        return $normalized;
    }

    /**
     * Строго валидирует, что пара входит в TradePair.
     *
     * @param  string $input
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function strictPair(string $input): string
    {
        $normalized = self::normalizePair($input);

        foreach (TradePair::cases() as $case) {
            if ($normalized === $case->value) {
                return $case->value;
            }
        }

        throw new \InvalidArgumentException("Неподдерживаемая торговая пара: $input");
    }

    /**
     * Извлекает только непустые (не null) поля из InvActivityInputDto в виде массива.
     * Полезно для обновлений, где нужно сохранить только переданные значения.
     *
     * @param  InvActivityInputDto $dto
     * @param  bool $strictPair Использовать строгую проверку пары
     * @return array<string, mixed> Ассоциативный массив непустых полей
     */
    public static function fromInputExtractFilledFields(InvActivityInputDto $dto, bool $strictPair = false): array
    {
        $fields = [];

        if ($dto->id !== null) {
            $fields['id'] = $dto->id;
        }

        if ($dto->InvActivityHash !== null) {
            $fields['activity_hash'] = $dto->InvActivityHash;
        }

        if ($dto->leadUid !== null) {
            $fields['lead_uid'] = $dto->leadUid;
        }

        if ($dto->type !== null) {
            $fields['type'] = $dto->type;
        }

        if ($dto->openTime !== null) {
            $fields['open_time'] = (new DateTimeImmutable($dto->openTime))->format('Y-m-d H:i:s');
        }

        if ($dto->closeTime !== null) {
            $fields['close_time'] = (new DateTimeImmutable($dto->closeTime))->format('Y-m-d H:i:s');
        }

        if ($dto->pair !== null) {
            $fields['pair'] = $strictPair ? self::strictPair($dto->pair) : self::normalizePair($dto->pair);
        }

        if ($dto->openPrice !== null) {
            $fields['open_price'] = $dto->openPrice;
        }

        if ($dto->closePrice !== null) {
            $fields['close_price'] = $dto->closePrice;
        }

        if ($dto->amount !== null) {
            $fields['amount'] = $dto->amount;
        }

        if ($dto->direction !== null) {
            $fields['direction'] = $dto->direction;
        }

        if ($dto->result !== null) {
            $fields['result'] = $dto->result;
        }

        return $fields;
    }

    /**
     * Извлекает только непустые (не null) поля из DbInvActivityDto в виде массива.
     * Используется для обновлений в базе данных.
     *
     * @param  DbInvActivityDto $dto
     * @param  bool $strictPair Использовать строгую проверку пары
     * @return array<string, mixed>
     */
    public static function fromDbExtractFilledFields(DbInvActivityDto $dto, bool $strictPair = false): array
    {
        $fields = [];

        if ($dto->id !== null) {
            $fields['id'] = $dto->id;
        }

        if ($dto->activity_hash !== null) {
            $fields['activity_hash'] = $dto->activity_hash;
        }

        if ($dto->lead_uid !== null) {
            $fields['lead_uid'] = $dto->lead_uid;
        }

        if ($dto->type !== null) {
            $fields['type'] = $dto->type;
        }

        if ($dto->open_time !== null) {
            $fields['open_time'] = (new DateTimeImmutable($dto->open_time))->format('Y-m-d H:i:s');
        }

        if ($dto->close_time !== null) {
            $fields['close_time'] = (new DateTimeImmutable($dto->close_time))->format('Y-m-d H:i:s');
        }

        if ($dto->pair !== null) {
            $fields['pair'] = $strictPair ? self::strictPair($dto->pair) : self::normalizePair($dto->pair);
        }

        if ($dto->open_price !== null) {
            $fields['open_price'] = $dto->open_price;
        }

        if ($dto->close_price !== null) {
            $fields['close_price'] = $dto->close_price;
        }

        if ($dto->amount !== null) {
            $fields['amount'] = $dto->amount;
        }

        if ($dto->direction !== null) {
            $fields['direction'] = $dto->direction;
        }

        if ($dto->result !== null) {
            $fields['result'] = $dto->result;
        }

        return $fields;
    }


    /**
     * Генерирует уникальный хеш сделки на основе ключевых полей.
     *
     * @param  InvActivityInputDto $dto
     * @return string
     */
    public static function generateActivityHash(InvActivityInputDto $dto): string
    {
        $leadUid = mb_strtolower(trim((string) $dto->leadUid));
        if ($leadUid === '') {
            throw new \InvalidArgumentException('leadUid обязателен для генерации хеша.');
        }

        $pair = $dto->pair ? self::normalizePair($dto->pair) : '';
        // $openTime = trim((string) $dto->openTime);
        $amount = $dto->amount !== null ? number_format($dto->amount, 8, '.', '') : '';
        $direction = $dto->direction ? mb_strtolower(trim($dto->direction)) : '';
        $openPrice = $dto->openPrice !== null ? number_format($dto->openPrice, 8, '.', '') : '';
        $type = $dto->type ? mb_strtolower(trim($dto->type)) : '';

        // Только значимые поля для идентификации сделки
        $fields = array_filter([
            $leadUid,
            // $openTime,
            $pair,
            $amount,
            $direction,
            $openPrice,
            $type
        ], fn($val) => $val !== '');

        $concatenated = implode('|', $fields);

        return hash('sha256', $concatenated);
    }
}
