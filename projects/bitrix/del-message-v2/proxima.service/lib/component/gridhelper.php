<?php


namespace Proxima\Service\Component;


use Bitrix\Main\Grid;
use Bitrix\Main\UI;
use Bitrix\Main\UI\Filter\Type;
use Bitrix\Main\UI\Filter\FieldAdapter;
use Exception;

/**
 * Class GridHelper
 * @package Proxima\Service\Component
 */
class GridHelper
{
    protected string $gridId;
    protected string $filterId;
    protected ?Grid\Options $gridOptions;
    protected ?UI\Filter\Options $filterOptions;
    protected ?UI\PageNavigation $navigation;
    protected array $filter = [];
    protected array $columns = [];
    protected array $rows = [];

    /**
     * GridHelper constructor.
     * @param string $gridId
     * @param string $filterId
     * @throws Exception
     */
    public function __construct(string $gridId, string $filterId = '')
    {
        if(empty($gridId))
            throw new Exception('Grid id can not be empty!');
        if(empty($filterId))
            $filterId = $gridId;
        $this->gridId = $gridId;
        $this->filterId = $filterId;
        $this->gridOptions = new Grid\Options($this->gridId);
        $this->filterOptions = new UI\Filter\Options($this->filterId);
        $this->navigation = new UI\PageNavigation($this->gridId);

        $navParams = $this->gridOptions->GetNavParams();
        $this->navigation->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();
    }

    /**
     * Set filter array for filter
     * @param array $filter
     * @return $this
     */
    public function setFilter(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Add field to filter array for filter
     * @param array $field
     * @return $this
     */
    public function addFilter(array $field): self
    {
        $this->filter[] = $field;
        return $this;
    }

    /**
     * Set columns array for grid
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add column to column array for grid
     * @param array $column
     * @return $this
     */
    public function addColumn(array $column): self
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Set rows array for grid
     * @param array $rows
     * @return $this
     */
    public function setRows(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Add row to row array for grid
     * @param array $row
     * @return $this
     */
    public function addRow(array $row): self
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Return grid id
     * @return string
     */
    public function getGridId(): string
    {
        return $this->gridId;
    }

    /**
     * Return filter id
     * @return string
     */
    public function getFilterId(): string
    {
        return $this->filterId;
    }

    /**
     * Return grid option object
     * @return Grid\Options
     */
    public function getGridOptions(): Grid\Options
    {
        return $this->gridOptions;
    }

    /**
     * Return filter option object
     * @return UI\Filter\Options
     */
    public function getFilterOptions(): UI\Filter\Options
    {
        return $this->filterOptions;
    }

    /**
     * Return filter array
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Return columns array
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Return rows array
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Return filter array from ORM 'filter'
     * @param array $destSelectorFieldsIDs
     * @return array
     */
    public function getFilterData(array $destSelectorFieldsIDs=[]): array
    {
        $sourceFields = $this->getFilter();
        $options = $this->getFilterOptions();

        $filter = $options->getFilter($sourceFields);
        $filterData = [];
        if ($filter["FILTER_APPLIED"] === true)
        {
            $filterData = self::getLogicFilter($filter, $sourceFields);
        }
        if(count($destSelectorFieldsIDs)){
            foreach($destSelectorFieldsIDs as $fieldId) {
                if (isset($filterData[$fieldId])) {
                    if (is_array($filterData[$fieldId])) {
                        foreach ($filterData[$fieldId] as $k => $user) {
                            $filterData[$fieldId][$k] = intval(preg_replace('/[^0-9]/', '', $user));
                        }
                    } else {
                        $filterData[$fieldId] = intval(preg_replace('/[^0-9]/', '', $filterData[$fieldId]));
                    }
                }
            }
        }
        return $filterData;
    }

    /**
     * Picks up from request filter data and converts it to ORM filter.
     * @param array $data
     * @param array $sourceFields
     * @return array
     */
    private static function getLogicFilter($data, array $sourceFields)
    {
        $types = Type::getInstance()->getTypesList();
        $result = [];

        foreach ($sourceFields as $sourceFieldKey => $sourceField)
        {
            $filter = array_merge(
                FieldAdapter::adapt($sourceField),
                array("STRICT" => $sourceField["strict"] === true)
            );

            if (array_key_exists($filter["TYPE"], $types) &&
                class_exists($types[$filter["TYPE"]]) &&
                is_callable(array($types[$filter["TYPE"]], "getLogicFilter")))
            {
                //------------------------
                if($types[$filter["TYPE"]]=="Bitrix\Main\UI\Filter\DateType") {
                    $res = _DateType::getLogicFilter($data, $filter);
                }
                //------------------------
                else {
                    $res = call_user_func_array(array($types[$filter["TYPE"]], "getLogicFilter"), array($data, $filter));
                }
                if (!empty($res))
                    $result += $res ;
            }
            elseif (array_key_exists($filter["NAME"], $data) && $data[$filter["NAME"]] <> '')
            {
                $result[$filter["NAME"]] = $data[$filter["NAME"]];
            }
            elseif ($filter["TYPE"]==Type::CUSTOM_DATE)
            {
                //@todo
            }
        }
        return $result;
    }

    /**
     * Return navigation object
     * @return UI\PageNavigation
     */
    public function getNavigation(): UI\PageNavigation
    {
        return $this->navigation;
    }

    /**
     * Return sort array fot ORM 'order'
     * @param array $default
     * @return array
     */
    public function getSort(array $default = ['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]): array
    {
        return $this->getGridOptions()->getSorting($default)['sort'];
    }
}