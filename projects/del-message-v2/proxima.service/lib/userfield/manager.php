<?php

namespace Proxima\Service\UserField;

use Bitrix\Bizproc\FieldType;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use CUserTypeManager;
use Exception;
use Proxima\Service\Activities\Field;
use Proxima\Service\Component\Form\BoolField;
use Proxima\Service\Component\Form\DateField;
use Proxima\Service\Component\Form\FileField;
use Proxima\Service\Component\Form\NumberField;
use Proxima\Service\Component\Form\SelectField;
use Proxima\Service\Component\Form\StringField;
use Proxima\Service\Component\Form\UserField;

class Manager
{
    protected string $entityId;
    protected array $fieldDescription;
    protected array $enumCache;

    /**
     * @param string $entityId
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws LoaderException
     */
    public function __construct(string $entityId)
    {
        $this->entityId = $entityId;
        $this->fieldDescription = (new CUserTypeManager())->GetUserFields(
            $this->getEntityId(),
            0,
            Application::getInstance()->getContext()->getLanguage()
        );

        //Collect list fields info
        $enumFields = [];
        $elementFields = [];
        $sectionFields = [];
        foreach ($this->getFieldDescription() as $field) {
            $fieldsId = $field['ID'];
            $fieldName = $field['FIELD_NAME'];
            $fieldType = $field['USER_TYPE']['USER_TYPE_ID'];
            if ($fieldType === 'enumeration') {
                $enumFields[$fieldsId] = $fieldName;
            } elseif ($fieldType === 'iblock_element') {
                $elementFields[$fieldName] = $field['SETTINGS']['IBLOCK_ID'];
            } elseif ($fieldType === 'iblock_section') {
                $sectionFields[$fieldName] = $field['SETTINGS']['IBLOCK_ID'];
            }
        }

        //Enum fields
        if (!empty($enumFields)) {
            $result = \CUserFieldEnum::GetList([], [
                'USER_FIELD_ID' => array_keys($enumFields),
            ]);
            while ($variant = $result->Fetch()) {
                $fieldsId = $variant['USER_FIELD_ID'];
                $fieldName = $enumFields[$fieldsId];
                $this->enumCache[$fieldName][$variant['ID']] = $variant['VALUE'];
            }
        }

        if (Loader::includeModule('iblock')) {
            //Iblock element bind fields
            if (!empty($elementFields)) {
                $result = ElementTable::getList([
                    'select' => ['ID', 'NAME', 'IBLOCK_ID'],
                    'filter' => ['=IBLOCK_ID' => array_values($elementFields)]
                ]);
                while ($element = $result->fetch()) {
                    foreach ($elementFields as $fieldName => $iblockId) {
                        if ($element['IBLOCK_ID'] == $iblockId) {
                            $this->enumCache[$fieldName][$element['ID']] = $element['NAME'];
                        }
                    }
                }
            }

            //Iblock section bind fields
            if (!empty($sectionFields)) {
                $result = SectionTable::getList([
                    'select' => ['ID', 'NAME', 'IBLOCK_ID'],
                    'filter' => ['=IBLOCK_ID' => array_values($sectionFields)]
                ]);
                while ($section = $result->fetch()) {
                    foreach ($sectionFields as $fieldName => $iblockId) {
                        if ($section['IBLOCK_ID'] == $iblockId) {
                            $this->enumCache[$fieldName][$section['ID']] = $section['NAME'];
                        }
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return array|mixed
     */
    public function getFieldDescription(): array
    {
        return $this->fieldDescription;
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getEnumList(string $fieldName): array
    {
        return $this->enumCache[$fieldName] ?? [];
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        $items = [];
        foreach ($this->getFieldDescription() as $field) {
            $item = [
                'id' => $field['FIELD_NAME'],
                'name' => $field['LIST_FILTER_LABEL'],
                'default' => false,
            ];
            switch ($field['USER_TYPE']['USER_TYPE_ID']) {
                case 'double':
                case 'integer':
                    $item['type'] = 'number';
                    break;
                case 'enumeration':
                case 'iblock_element':
                case 'iblock_section':
                    $item['type'] = 'list';
                    $item['items'] = $this->getEnumList($field['FIELD_NAME']);
                    $item['params']['multiple'] = true;
                    break;
                case 'boolean':
                    $item['type'] = 'list';
                    $item['items'] = [
                        '1' => GetMessage('MAIN_YES'),
                        '0' => GetMessage('MAIN_NO'),
                    ];
                    break;
                case 'date':
                case 'datetime':
                    $item['type'] = 'date';
                    break;
                case 'file':
                    continue 2;
                case 'employee':
                    $item['type'] = 'dest_selector';
                    $item['user_type_id'] = 'employee';
                    $item['params'] = [
                        //apiVersion' => 3,
                        'context' => $field['FIELD_NAME'],
                        'contextCode' => 'U',
                        'enableUsers' => 'Y',
                        'userSearchArea' => 'I',
                        'allowUserSearch' => 'Y',
                        'multiple' => 'Y'
                    ];
                    break;
                default:
                    $item['type'] = 'string';
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $items = [];
        foreach ($this->getFieldDescription() as $field) {
            $items[] = [
                'id' => $field['FIELD_NAME'],
                'name' => $field['LIST_COLUMN_LABEL'],
                'sort' => $field['FIELD_NAME'],
                'default' => false,
            ];
        }
        return $items;
    }

    /**
     * @param EntityObject $entity
     * @param bool $printable
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public function getValuesFromEntity(EntityObject $entity, bool $printable = true): array
    {
        $data = [];
        foreach ($this->getFieldDescription() as $field) {
            $data[$field['FIELD_NAME']] = $entity->get($field['FIELD_NAME']);
        }
        return $this->getValuesFromArray($data, $printable);
    }

    /**
     * @param array $data
     * @param bool $printable
     * @return array
     */
    public function getValuesFromArray(array $data, bool $printable = true): array
    {
        $items = [];
        foreach ($this->getFieldDescription() as $field) {
            $rawValue = $data[$field['FIELD_NAME']];
            if ($printable) {
                switch ($field['USER_TYPE']['USER_TYPE_ID']) {
                    case 'enumeration':
                    case 'iblock_element':
                    case 'iblock_section':
                        $list = self::getEnumList($field['FIELD_NAME']);
                        $value = $list[$rawValue] ?? '-';
                        break;
                    case 'boolean':
                        $value = $data[$field['FIELD_NAME']] == 1 ? 'Да' : 'Нет';
                        break;
                    case 'file':
                        $values = [];
                        $fileIds = is_array($data[$field['FIELD_NAME']]) ? $data[$field['FIELD_NAME']] : [$data[$field['FIELD_NAME']]];
                        foreach ($fileIds as $fileId) {
                            if ($fileId) {
                                $file = \CFile::GetFileArray($fileId);
                                if ($file) {
                                    $values[] = '<a target="_blank" href="' . $file['SRC'] . '">' . $file['ORIGINAL_NAME'] . '</a>';
                                }
                            }
                        }
                        $value = implode(', ', $values);
                        break;
                    case 'employee':
                        $value = '';
                        if ($rawValue) {
                            $value = '<a href="/company/personal/user/' . $rawValue . '/">Пользователь #' . $rawValue . '</a>';
                        }
                        break;
                    default:
                        $value = strval($data[$field['FIELD_NAME']]);
                }
            } else {
                $value = $rawValue;
            }
            $items[$field['FIELD_NAME']] = $value;
        }
        return $items;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getFieldList(array $data): array
    {
        $items = [];
        foreach ($this->getFieldDescription() as $field) {
            $fieldName = $field['FIELD_NAME'];
            $fieldType = $field['USER_TYPE']['USER_TYPE_ID'];
            $rawValue = $data[$fieldName];
            switch ($fieldType) {
                case 'double':
                case 'integer':
                    $step = 1;
                    if ($fieldType === 'double') {
                        $precision = intval($field['SETTINGS']['PRECISION']);
                        if ($precision > 0) {
                            $step = 1 / pow(10, $precision);
                        }
                    }
                    $item = (new NumberField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                            'min' => $field['SETTINGS']['MIN_VALUE'],
                            'max' => $field['SETTINGS']['MAX_VALUE'],
                            'step' => $step,
                        ])->setValue($rawValue);
                    break;
                case 'enumeration':
                case 'iblock_element':
                case 'iblock_section':
                    $item = (new SelectField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                            'items' => $this->getEnumList($fieldName),
                            'use_empty' => true,
                        ])->setValue($rawValue);
                    break;
                case 'boolean':
                    $item = (new BoolField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                        ])->setValue($rawValue);
                    break;
                case 'date':
                case 'datetime':
                    $item = (new DateField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                            'time' => ($fieldType === 'datetime')
                        ])->setValue($rawValue);
                    break;
                case 'file':
                    $item = (new FileField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                            'theme' => FileField::SIMPLE
                        ])->setValue($rawValue);
                    break;
                case 'employee':
                    $item = (new UserField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                        ])->setValue($rawValue);
                    break;
                default:
                    $item = (new StringField())
                        ->setName($fieldName)
                        ->setTitle($field['EDIT_FORM_LABEL'])
                        ->setOption([
                            'required' => ($field['MANDATORY'] === 'Y'),
                            'disabled' => ($field['EDIT_IN_LIST'] === 'N'),
                        ])->setValue($rawValue);
                    break;
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @param string $prefix
     * @return array
     * @throws Exception
     */
    public function getActivityFieldList(string $prefix = ''): array
    {
        if (!Loader::includeModule('bizproc')) {
            throw new Exception('Error load module bizproc');
        }
        $items = [];
        foreach ($this->getFieldDescription() as $field) {
            $fieldName = $field['FIELD_NAME'];
            $fieldType = $field['USER_TYPE']['USER_TYPE_ID'];
            switch ($fieldType) {
                case 'double':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::DOUBLE,
                        ($field['MANDATORY'] === 'Y')
                    );
                    break;
                case 'integer':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::INT,
                        ($field['MANDATORY'] === 'Y')
                    );
                    break;
                case 'enumeration':
                case 'iblock_element':
                case 'iblock_section':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::SELECT,
                        ($field['MANDATORY'] === 'Y'),
                        $this->getEnumList($fieldName)
                    );
                    break;
                case 'boolean':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::BOOL,
                        ($field['MANDATORY'] === 'Y'),
                        $this->getEnumList($fieldName)
                    );
                    break;
                case 'date':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::DATE,
                        ($field['MANDATORY'] === 'Y'),
                        $this->getEnumList($fieldName)
                    );
                    break;
                case 'datetime':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::DATETIME,
                        ($field['MANDATORY'] === 'Y'),
                        $this->getEnumList($fieldName)
                    );
                    break;
                case 'file':
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::FILE,
                        ($field['MANDATORY'] === 'Y')
                    );
                    break;
                default:
                    $item = new Field(
                        $prefix . $fieldName,
                        $field['EDIT_FORM_LABEL'],
                        FieldType::STRING,
                        ($field['MANDATORY'] === 'Y'),
                        $this->getEnumList($fieldName)
                    );
                    break;
            }
            $items[] = $item;
        }
        return $items;
    }
}