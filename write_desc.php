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
        "route" => "div[id=evs_container] > div[class=eventinside]",
        "type" => "subcontent",
        "param" => array(
            "id" => array(
                "route" => "a[class=link]",
                "type" => "atribute",
                "param" => array(
                    "value" => "href",
                ),
            ),
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
            "read_more" => array(
                "route" => "a[class=link]",
                "type" => "atribute",
                "param" => array(
                    "value" => "href",
                ),
            ),
        ),
    ),
);



$fp = fopen($file_name, 'w');
fwrite($fp, json_encode($desc));
fclose($fp);

print_r(json_encode($desc));
?>