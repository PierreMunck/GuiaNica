<?php
include_once('lib/parser/Rss.php');

$parser = new Parserurl();
$parser->setUrl("http://experiencias.folkano.com");
$parser->parse();
$parser->setDescription(__DIR__."/desc/experiencias_folkano_com.desc");
$result = $parser->getInfo();
print_r($result);
?>