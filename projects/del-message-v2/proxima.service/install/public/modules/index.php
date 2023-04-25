<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Модули");
?><?$APPLICATION->IncludeComponent(
	"proxima:service.module.list",
	"",
	Array(
		"SEF_FOLDER" => "/proxima/service/modules/",
		"SEF_MODE" => "Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>