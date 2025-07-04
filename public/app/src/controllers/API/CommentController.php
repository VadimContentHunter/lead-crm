<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\_common\repositories\CommentRepository;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\_common\adapters\CommentValidatorAdapter;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\CommentManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\components\CommentManagement\_common\mappers\CommentMapper;

class CommentController
{
    private CommentManagement $commentManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->commentManagement = new CommentManagement(
            new CommentRepository($pdo, $logger),
            new CommentValidatorAdapter()
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'comment.add':
                $this->addComment($this->rpc->getParams());
            // break;

            case 'comment.get.all':
                $this->getComments($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function addComment(array $params): void
    {
        $leadId = $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (!is_string($params['comment'] ?? null)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $executeResult = $this->commentManagement->create()->execute(CommentMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            $this->rpc->replyData([
                ['type' => 'success', 'message' => 'Комментарий успешно добавлен'],
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Комментарий не был добавлен. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getComments(array $params): void
    {
        $leadId = $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (!is_string($params['comment'] ?? null)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $executeResult = $this->commentManagement->get()->executeAllMapped(function (Comment $comment) {
            return $comment->comment;
        })->getArray();

        if (count($executeResult) > 0) {
            $this->rpc->replyData([
                ['type' => 'success', 'comments' => $executeResult],
             ]);
        } else {
            $this->rpc->replyData([
                ['type' => 'success', 'comments' => []],
             ]);
        }
    }
}
