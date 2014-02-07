<?php

include_once 'lib/Curl.php';
include_once 'simple_html_dom.php';


define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT',    3);
define('HDOM_TYPE_ENDTAG',  4);
define('HDOM_TYPE_ROOT',    5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO',     3);
define('HDOM_INFO_BEGIN',   0);
define('HDOM_INFO_END',     1);
define('HDOM_INFO_QUOTE',   2);
define('HDOM_INFO_SPACE',   3);
define('HDOM_INFO_TEXT',    4);
define('HDOM_INFO_INNER',   5);
define('HDOM_INFO_OUTER',   6);
define('HDOM_INFO_ENDSPACE',7);
define('DEFAULT_TARGET_CHARSET', 'UTF-8');
define('DEFAULT_BR_TEXT', "\r\n");
define('DEFAULT_SPAN_TEXT', " ");
define('MAX_FILE_SIZE', 600000);

class ParserUrl {
    private $url = NULL;
    private $content = NULL;
    private $dom = NULL;
    
    public function setUrl($url)
    {
        $regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        if(preg_match($regex, $url)){
            $this->url = $url;
        }
    }
    
    public function getUrl($url)
    {
        return $this->url;
    }
    
    public function parse()
    {
        if(isset($this->url)&& $this->url != ""){
            $curl = new Curl();
            $curl->exec($this->url);
            if($curl->getHeader('http_code') == 200){
                $this->content = $curl->getResult();
            }
        }
        
        if(isset($this->content)&& $this->content != ""){
            
            $lowercase = true;
            $forceTagsClosed=true;
            $stripRN=true;
            
            $this->dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, DEFAULT_TARGET_CHARSET, $stripRN, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);
            
            $this->dom->load($this->content, $lowercase, $stripRN);
        }
        return true;
    }
    
    public function getInfo(){
        
        foreach($this->dom->find('article') as $article) {
            $h1 = $article->find('h1',0);
            echo "title = ".$h1->text() . "<br/>\n";
            $img = $article->find('img',0);
            echo "image = ". $img->getAttribute('src') . "<br/>\n";
            foreach($article->find('span[class=foo]') as $info) {
                if( sizeof($info->find('i[class=icon-calendar]')) > 0){
                    echo "fecha = ". $info->text() . "<br/>\n";
                }
                if( sizeof($info->find('i[class=icon-time]')) > 0){
                    echo "horario = ". $info->text() . "<br/>\n";
                }
                if( sizeof($info->find('i[class=icon-map-marker]')) > 0){
                    $lugarinf = $info->find('span',0);
                    $lugar = $lugarinf->find('a',0);
                    echo "lugar = ". $lugarinf->text() . "<br/>\n";
                }
            }
            $desc = $article->find('div[class=description]',0);
            echo "description = ". $desc->text() . "<br/>\n";
        }
    }
}
?>   