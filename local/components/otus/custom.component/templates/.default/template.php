<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div style="padding: 15px; border: 1px dashed #2196f3; background: #e3f2fd;">
    <b>Курс валюты:</b> 1 <?= $arResult['CURRENCY'] ?> = <?= number_format($arResult['AMOUNT'], 2, '.') ?> RUB
</div>
