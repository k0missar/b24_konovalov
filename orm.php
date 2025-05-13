<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/services/index.php");
$APPLICATION->SetTitle("ORM TEST");

use Bitrix\Main\Loader;
use Bitrix\Iblock\Iblock;

Loader::includeModule("iblock");

$iblockId = 17; // doctors
?>

<?php // Стандартная выборка
$res = CIBlockElement::GetList(
    Array("SORT"=>"ASC"),
    Array('IBLOCK_ID' => 17),
    false,
	false,
	Array('ID', 'NAME', 'PROPERTY_PROTSEDURA', 'PROPERTY_O_VRACHE')
);

while($ob = $res->fetch()) {
    echo '<pre>' . print_r($ob, 1) . '</pre>';
}
?>
<hr>
<?php // ORM - конкретный элемент
	$iblock = Iblock::wakeUp($iblockId);
	$element = $iblock->getEntityDataClass()::getByPrimary(37, [
		'select' => ['NAME', 'PROTSEDURA', 'O_VRACHE']
	])->fetchObject();

	echo $element->get('NAME') . '<br>';
	//echo $element->get('PROTSEDURA')->getValue() . '<br>';
	echo $element->get('O_VRACHE')->getValue() . '<br><hr>';
?>
<hr>
<?php // ORM - список элементов
	$arElement = Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
		'select' => ['NAME', 'PROTSEDURA', 'O_VRACHE']
	])->fetchCollection();

	foreach ($arElement as $element) {
		echo $element->getName() . '<br>';
		//echo $element->getProtsedura()->getValue() . '<br>';
		echo $element->getOVrache()->getValue() . '<br><hr>';
	}
?>
<hr>

<?php // ORM - список элементов
$arElement = Bitrix\Iblock\Elements\ElementDoctorsTable::query()
	->addSelect('NAME')
	->addSelect('O_VRACHE')
	->fetchCollection();

foreach ($arElement as $element) {
	echo $element->getName() . '<br>';
	//echo $element->getProtsedura()->getValue() . '<br>';
	echo $element->getOVrache()->getValue() . '<br><hr>';
}
?>
<hr>

<?php // ORM - ElementTable - Свойств получаются отдельно
$arElement = Bitrix\Iblock\ElementTable::getList([
	'select' => ['NAME'],
	'filter' => ['IBLOCK_ID' => 17],
]);

foreach ($arElement as $element) {
	echo $element['NAME'] . '<br><hr>';
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
