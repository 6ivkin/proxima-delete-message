<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обязательные поля");
?><?$APPLICATION->IncludeComponent(
    "proxima:service.crm.require_field.list",
    "",
    Array()
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>