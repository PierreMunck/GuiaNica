<?php
include_once 'lib/Parser.php';
include_once 'lib/parser/Url.php';

class ParserRss extends Parser{

    private $stockFileDir = NULL;
    private $stockFileName = NULL;
    
    public function __construct($fileDir){
        $this->setStockFileDir($fileDir);
    }
    
    public function setStockFileDir($fileDir){
        $this->stockFileDir = $fileDir;
    }

    private function saveContentInFile($file_name){
        $fp = fopen($file_name, 'w');
        fwrite($fp, $this->content);
        fclose($fp);
    }
    
    private function loadContentInFile($file_name){
        $fp = fopen($file_name, "r");
        $this->content = fread($fp, filesize($file_name)); 
        fclose($fp);
    }
    
    private function generateFileName($fecha){
        //$time = strtotime($fecha);
        $name = str_replace("http://","",$this->url);
        $name = trim ( $name ,"/\\");
        $name = str_replace("/","_",$name);
        $name = str_replace(".","_",$name);
        //$name .= "-".$time.".dat";
        $name .= ".dat";
        $this->stockFileName = $name;
    }
    
    public function need_rebuild(){
        $find = $this->dom->find('lastBuildDate',0);
        $fecha =$this->getTextInfoVal($find);
        $this->generateFileName($fecha);
        
        if(file_exists($this->stockFileDir."/".$this->stockFileName)){
            $this->loadContentInFile($this->stockFileDir."/".$this->stockFileName);
            print_r("load_in cache");
            return false;
        }
        return true;
    }
    
    public function getInfo(){
        if($this->need_rebuild()){
            $this->result = array();
            if(!is_array($this->description)){
                return NULL;
            }
            foreach ($this->description as $key => $info) {
                $route = explode(">", $info["route"]);
                $this->result[$key] = $this->getRecursiveInfo($this->dom,$route,$info);
            }
            
            $this->content = json_encode($this->result);
            $this->saveContentInFile($this->stockFileDir."/".$this->stockFileName);
        }
        return json_decode($this->content,true);
    }
    
    protected function getUrlInfoVal($obj,$param){
        $url = $this->getTextInfoVal($obj);
        
        $url = str_replace("http://folkano.com/", "http://experiencias.folkano.com/", $url);
        $parser = new ParserUrl();
        $parser->setUrl($url);
        $parser->parse();
        $parser->setDescription($param);
        return $parser->getInfo();
    }

}
?>