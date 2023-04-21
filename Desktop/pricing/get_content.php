<?php
require_once './class/Parser.php';
require_once './class/DAO.php';

$parser = new Parser();
$connection = new DAO();

$products = $parser->getProductsLinks('https://saratov.metal100.ru/prodazha/Truboprovodnaya-armatura/Flanets_stalnoy');

$info = [];

foreach ($products as $product) {
    foreach ($parser->getInfo($product['url']) as $item) {
        $info[] = $item;
    }
}

$connection->makeUpload($info);
