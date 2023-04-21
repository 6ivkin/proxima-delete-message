<?

namespace Proxima\Service\Component;
use Bitrix\Main\UI\Filter\DateType;


/**
 * Class DateType. Available subtypes of date field
 * @package Proxima\Service\Component
 */
class _DateType extends DateType
{
    /**
     * Search in plain array data that can belongs to this type.
     * @param array $data
     * @param array $filterFields
     * @return array
     */
    public static function getLogicFilter(array $data, array $filterFields)
    {
        $filter = [];
        $keys = array_filter($data, function($key) { return (mb_substr($key, 0 - mb_strlen(self::getPostfix())) == self::getPostfix()); }, ARRAY_FILTER_USE_KEY);
        //echo "keys<pre>";print_r($filterFields);echo "</pre>";
        foreach ($keys as $key => $val)
        {
            $id = mb_substr($key, 0, 0 - mb_strlen(self::getPostfix()));
            if($id!=$filterFields["NAME"])
                continue;
            if (array_key_exists($id."_from", $data))
                $filter[">=".$id] = $data[$id."_from"];
            if (array_key_exists($id."_to", $data))
                $filter["<=".$id] = $data[$id."_to"];
            break;
        }
        return $filter;
    }
}