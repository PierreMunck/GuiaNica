<?php

function generateFileName($url){
    //$time = strtotime($fecha);
    $name = str_replace("http://","",$url);
    $name = trim ( $name ,"/\\");
    $name = str_replace("/","_",$name);
    $name = str_replace(".","_",$name);
    //$name .= "-".$time.".dat";
    $name .= ".desc";
    return $name;
}


$file_name = __DIR__."/desc/".generateFileName("http://experiencias.folkano.com");

$desc = array(
    "item" => array(
        "route" => "evs_container > eventinside",
        "type" => "subcontent",
        "param" => array(
            "title" => array(
                "route" => "span[class=title]",
                "type" => "text",
            ),
            "img" => array(
                "route" => "img[class=ev_img]",
                "type" => "atribute",
                "param" => array(
                    "value" => "src",
                ),
            ),
            "fecha" => array(
                "route" => "div[class=evnt-date]",
                "type" => "text",
            ),
            "lugar" => array(
                "route" => "span[class=subtitle]",
                "type" => "text",
            ),
        ),
    ),
);



$fp = fopen($file_name, 'w');
fwrite($fp, json_encode($desc));
fclose($fp);

?>