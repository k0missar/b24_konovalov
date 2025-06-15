<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>

<?php
$APPLICATION->IncludeComponent(
    'otus:custom.component',
    '',
    [
        'CURRENCY' => '',
    ]
);
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
