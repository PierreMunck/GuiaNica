<?php
include_once('lib/parser/Url.php');

$parser = new ParserUrl();

//$parser->setUrl("http://experiencias.folkano.com/rss/");
$parser->setUrl("http://experiencias.folkano.com/flkn/exposicion-huellas-spuren/");

$parser->parse();

$parser->getInfo();
?>