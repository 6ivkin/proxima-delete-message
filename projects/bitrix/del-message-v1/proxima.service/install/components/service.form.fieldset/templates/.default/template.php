<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;

Extension::load(['ui.alerts', 'ui.forms', 'proxima.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceFormFieldset $component */
$component = $this->getComponent();
?>
<?php foreach($component->getErrorsCompatible() as $error) :?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<div class="itb-fieldset-container">
    <?php foreach ($component->getFields() as $field) : ?>
        <div id="itb-field-container-<?= $field->getCssName() ?>" class="itb-field-container <?= $field->getCssClassString() ?>" <?= $field->getDataString() ?> >
            <?php $APPLICATION->IncludeComponent('proxima:service.form.field', '', ['FIELD' => $field]); ?>
        </div>
    <?php endforeach; ?>
</div>

