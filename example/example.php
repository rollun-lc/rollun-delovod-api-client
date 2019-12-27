<?php

use rollun\delovod\Api\DataStores\AbstractCatalogs;
use rollun\delovod\Api\DataStores\AbstractDocuments;
use rollun\delovod\Api\DelovodApi;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Query;

$dir = __DIR__;
echo '$dir\n';
chdir(dirname(dirname($dir)));
require 'vendor/autoload.php';

$apiKey = '';

//Init client
$delovodApi = new DelovodApi($apiKey);


//Create Abstract document store with type `documents.sale`
$amazonDsDocSale = new AbstractDocuments($delovodApi, 'documents.sale', [
    //predefined field (use in query and create methods)
    'firm' => '1100400000001001',
    'business' => '1115000000000001',
    'person' => '1100100000001007',
    'currency' => '1101200000001001',
    'author' => '1000200000001002',
    'paymentForm' => '1110300000000001',
    'costItem' => '1106100000000003',
    'incomeItem' => '1106500000000002',
    'operationType' => '1004000000000018',
]);

//create doc
$doc = $amazonDsDocSale->create([
    'header' => [
        'remark' => 'test'
    ]
]);
print_r($doc);

//create doc
$doc = $amazonDsDocSale->update([
    'header' => [
        'id' => '{}',
        'remark' => 'test2'
    ]
]);
print_r($doc);

//query docs
$query = new Query();
$query->setSelect(new SelectNode([
    "id",
    "remark",
    "delMark",
]));
$query->setQuery(new AndNode([
    new EqNode('remark', 'test'),
    new EqNode('delMark', 0),
]));
$docs = $amazonDsDocSale->query($query);
print_r($docs);


//read docs
$doc = $amazonDsDocSale->read('{some_id}');
print_r($doc);


//Create abstract catalog store

$catalogGoodsApi = new AbstractCatalogs($delovodApi, 'catalogs.goods', [

]);