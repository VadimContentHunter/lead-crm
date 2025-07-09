<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\_common\repositories\CommentRepository;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\_common\adapters\CommentValidatorAdapter;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\CommentManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\components\CommentManagement\_common\mappers\CommentMapper;

class CommentController
{
    private CommentManagement $commentManagement;

    private JsonRpcServerFacade $rpc;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->commentManagement = $this->appContext->getCommentManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var CommentController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'comment.add' => fn() => $secureCall->addComment($this->rpc->getParams()),
            'comment.get.all' => fn() => $secureCall->getComments($this->rpc->getParams())
        ];
    }

    public function init(): void
    {
        try {
            $method = $this->rpc->getMethod();

            if (!isset($this->methods[$method])) {
                throw new JsonRpcSecurityException('Метод не найден', -32601);
            }

            ($this->methods[$method])();
        } catch (JsonRpcSecurityException $e) {
            $this->rpc->send($e->toJsonRpcError($this->rpc->getId()));
        } catch (\Throwable $e) {
            $this->rpc->replyError(-32000, $e->getMessage());
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

        $commentDto = CommentMapper::fromArray($params);
        if ($commentDto === null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Некорректные данные для создания Депозита.']
            ]);
        }

        $executeResult = $this->commentManagement->create()->execute($commentDto);
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
