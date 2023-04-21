<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;

Extension::load(["ui.forms", "ui.alerts", "ui.dialogs.messagebox", "proxima.bootstrap4"]);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceSystemCleaner $component */
$component = $this->getComponent();
?>
<?php foreach($component->getErrorsCompatible() as $error) :  ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php $APPLICATION->SetTitle("Очистка системы"); ?>
<?php if ($component->isHaveAccess()): ?>
    <div class="row">
        <div class="col col-4">
            <?php foreach ($component->getItems() as $item): ?>
                <?php
                $count = $item->getCount();
                ?>
                <div>
                    <h5><?= $item->getName() ?></h5>
                    <?php if ($count > 0): ?>
                        <button class="ui-btn ui-btn-success itb-repair-button" data-class="<?= $item::class ?>">
                            Исправить <i class="ui-btn-counter"><?= $count ?></i>
                        </button>
                    <?php else: ?>
                        <div class="alert alert-success">Проблем не найдено</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col">
            <h5>Обработка</h5>
            <div class="pb-3">
                <div class="ui-ctl ui-ctl-textbox ui-ctl-inline">
                    <input id="itb-repair-id" type="text" class="ui-ctl-element" value="0">
                </div>
                <div class="ui-ctl ui-ctl-textbox ui-ctl-inline">
                    <input id="itb-repair-count"  type="text" class="ui-ctl-element" readonly>
                </div>
                <button id="itb-stop-button" class="ui-btn ui-btn-danger" disabled>Остановить</button>
            </div>
            <table id="itb-repair-table" class="table table-sm table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Статус</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>



