<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Proxima\Service\Cleaner\ClosedDealTask;
use Proxima\Service\Cleaner\ClosedDealWorkflow;
use Proxima\Service\Cleaner\ClosedLeadTask;
use Proxima\Service\Cleaner\ClosedLeadWorkflow;
use Proxima\Service\Cleaner\ItemInterface;
use Proxima\Service\Cleaner\NoActiveUserTask;
use Proxima\Service\Cleaner\Result;
use Proxima\Service\Component\Simple;
use Proxima\Service\Log;

if(!Loader::includeModule("proxima.service")) {
    throw new Exception("Ошибка подключения модуля proxima.service");
}

class CITBServiceSystemCleaner extends Simple
{
    protected bool $haveAccess;
    /**
     * @var ItemInterface[]
     */
    protected array $items;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->haveAccess = CurrentUser::get()->isAdmin();
        $this->items = [];
    }

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            if (!$this->haveAccess) {
                throw new Exception('Нет доступа');
            }
            $this->items = [
                new NoActiveUserTask(),
                new ClosedLeadTask(),
                new ClosedDealTask(),
                new ClosedLeadWorkflow(),
                new ClosedDealWorkflow(),
            ];
        } catch (Exception $e) {
            $this->addErrorCompatible($e->getMessage());
        }
        $this->IncludeComponentTemplate();
    }

    /**
     * @return bool
     */
    public function isHaveAccess(): bool
    {
        return $this->haveAccess;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param int $id
     * @param string $item
     * @return Result
     */
    public function repairAction(int $id, string $item): Result
    {
        $result = new Result();
        $log = Log::get('repair');
        try {
            if (!$this->haveAccess) {
                throw new Exception('Нет доступа');
            }
            if (!is_a($item, ItemInterface::class, true)) {
                throw new Exception('Некорректный класс ' . $item);
            }
            /** @var ItemInterface $instance */
            $instance = new $item();
            $result = $instance->repair($id);
            if ($result->getCode() === Result::COMPLETE || $result->getCode() === Result::SUCCESS) {
                $log->ok('[' . $item . '] ' . $id . ' -> ' . $result->getRepairId());
            } else if ($result->getCode() === Result::WARNING) {
                $log->warn('[' . $item . '] ' . $id . ' -> ' . $result->getRepairId() . ' ' . $result->getMessage());
            } else if ($result->getCode() === Result::FAIL) {
                $log->error('[' . $item . '] ' . $id . ' -> ' . $result->getRepairId() . ' ' . $result->getMessage());
            } else {
                throw new Exception('Непредвиденная ошибка: ' . $result->getMessage());
            }
        } catch (Exception $e) {
            $this->addError(new \Bitrix\Main\Error($e->getMessage()));
            $log->error($e->getMessage());
            $result->setCode(Result::FAIL);
            $result->setMessage($e->getMessage());
        }
        return $result;
    }
}