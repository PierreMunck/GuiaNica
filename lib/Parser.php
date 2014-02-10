<?php

include_once 'lib/Curl.php';
include_once 'parser/simple_html_dom.php';


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

class Parser {
    protected $url = NULL;
    protected $content = NULL;
    protected $dom = NULL;
    protected $description = NULL;
    
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
    
    public function setDescription($description){
        $this->description = NULL;
        if(is_array($description)){
            $this->description = $description;
        }elseif(is_string($description) && file_exists($description)){
            $fp = fopen($description, "r");
            $description = fread($fp, filesize($description));
            $this->description = json_decode($description,true);
            fclose($fp);
        }
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function getContent(){
        return $this->content;
    }
    
    public function parse()
    {
        if(isset($this->url)&& $this->url != ""){
            try{
                $curl = new Curl();
                $curl->exec($this->url);
                if($curl->getHeader('http_code') == 200){
                    $this->content = $curl->getResult();
                }
            }catch(exception $e){
                $this->content = "";
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
    
    // recursive get value
    protected function getRecursiveInfo($obj,$route,$description){
        $result = NULL;
        if(sizeof($route) == 1){
            $method = "get".ucfirst(strtolower ( $description["type"] ))."InfoVal";
            if(!method_exists ( $this , $method )){
                return NULL;
            }
            print_r($method);
            $find = $obj->find($route[0]);
            if( sizeof($find) > 1){
                $result = array();
                $limit = 1000;
                if(isset($description["limit"]) && is_int($description["limit"])){
                    $limit = $description["limit"];
                }
                $item = 0;
                foreach ($find as $findValue) {
                    print_r($method);
                    $value = NULL;
                    if(isset($description["param"])){
                       $value = $this->$method($findValue,$description["param"]);
                    }else{
                        $value = $this->$method($findValue);
                    }
                    if($value != NULL){
                        $result[] = $value;
                        $item++;
                    }
                    if($item == $limit){
                        break;
                    }
                }
                if(sizeof($result) == 0){
                    $result = NULL;
                }elseif(sizeof($result) == 1){
                    $result = $result[0];
                }
            }
            if( sizeof($find) == 1){
                if(isset($description["param"])){
                   $result = $this->$method($find[0],$description["param"]);
                }else{
                    $result = $this->$method($find[0]);
                }
            }
        }else{
            $find = $obj->find($route[0]);
            array_shift($route);
            if( sizeof($find) > 1){
                $result = array();
                foreach ($find as $findValue) {
                    $result[] = $this->getRecursiveInfo($findValue,$route,$description);
                }
            }
            if( sizeof($find) == 1){
                $result = $this->getRecursiveInfo($find[0],$route,$description);
            }
        }
        return $result;
    }
    
    
    public function getInfo(){
        $result = array();
        if(!is_array($this->description)){
            return NULL;
        }
        foreach ($this->description as $key => $info) {
            $route = explode(">", $info["route"]);
            $result[$key] = $this->getRecursiveInfo($this->dom,$route,$info);
        }
        return $result;
    }
    
    protected function getTextInfoVal($obj){
        $val = trim ( $obj->text(), " \t\n\r\0\x0B" );
        $val = preg_replace("([ ]+)", " ", $val);
        return $val;
    }
    
    protected function getAtributeInfoVal($obj,$param){
        return $obj->getAttribute($param["value"]);
    }
    
    protected function getConditionalInfoVal($obj,$param){
        if($param["type"] == "constain"){
            if( sizeof($obj->find($param["value"])) > 0){
                return $this->getTextInfoVal($obj);
            }
        }
        return NULL;
    }
    
    protected function getSubcontentInfoVal($obj,$param){
        print_r($param);
        foreach ($param as $key => $info) {
            $route = explode(">", $info["route"]);
            
            $result[$key] = $this->getRecursiveInfo($obj,$route,$info);
        }
        print_r($result);
        return $result;
    }
    
    
}
?>