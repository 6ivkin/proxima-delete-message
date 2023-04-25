<?php


namespace Proxima\Service\Component;


use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use CBitrixComponent;

/**
 * Class Base
 * @package Proxima\Service\Component
 */
class Base extends CBitrixComponent implements Controllerable, Errorable
{
    protected ?RouterHelper $route;
    protected ?GridHelper $grid;
    protected ErrorCollection $errorCollection;

    /**
     * Base constructor.
     * @param null $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->route = null;
        $this->grid = null;
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [];
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    /**
     * @param $code
     * @return Error|null
     */
    public function getErrorByCode($code): ?Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    /**
     * @return Error[]
     */
    public function getErrorsCompatible(): array
    {
        return $this->errorCollection->toArray();
    }

    /**
     * @param Error $error
     * @return void
     */
    protected function addError(Error $error)
    {
        $this->errorCollection->add([$error]);
    }

    /**
     * @param string $message
     * @param $code
     * @param $customData
     * @return void
     */
    protected function addErrorCompatible(string $message, $code = 0, $customData = null)
    {
        $error = new Error($message, $code, $customData);
        $this->addError($error);
    }

    /**
     * @return mixed
     */
    public function getRoute(): ?RouterHelper
    {
        return $this->route;
    }

    /**
     * @param RouterHelper $route
     */
    public function setRoute(RouterHelper $route): void
    {
        $this->route = $route;
    }

    /**
     * @return GridHelper|null
     */
    public function getGrid(): ?GridHelper
    {
        return $this->grid;
    }

    /**
     * @param GridHelper $grid
     */
    public function setGrid(GridHelper $grid): void
    {
        $this->grid = $grid;
    }
}