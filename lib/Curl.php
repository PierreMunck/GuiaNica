<?php
/**
 * Class to handle cURL requests
 * 
 * @author Pierre Munck  www.munck.fr  twitter.com/PierreMunck  facebook.com/pierre.munck
 */
class Curl {
  /**
   * Contains the vars to send by POST
   * @var array
   */
  private $postVars;
  
  /**
   * Contains the vars to send by GET
   * @var array
   */
  private $getVars;
  
  /**
   * cURL handler
   * @var ressource
   */
  private $ch;
  
  /**
   * The headers to send
   * @var string
   */
  private $headers;
  
  /**
   * The option to send
   * @var string
   */
  private $options;
  
  /**
   * The number of the current channel
   * @var int
   */
  private $n;
  
  /**
   * The resulted text
   * @var string
   */
  private $r_text;
  
  /**
   * The resulted headers
   * @var string
   */
  private $r_headers;
  
  /**
   * The resulted headers info
   * @var string
   */
  private $r_info;
  
  /**
   * The resulted headers code
   * @var string
   */
  private $r_code;
  
  /**
   * Constructor
   */
  public function __construct($n = 0, $cookiePath = NULL) {
    if(is_null($cookiePath)){
      $cookiePath = realpath(dirname(__FILE__));
    }
    $defaultHeaders = array();
    $defaultHeaders['agent']   = 'User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)';
    $defaultHeaders['cookie']    = $cookiePath.'/cookies/'.$n;
    $defaultHeaders['randDate']  = mktime(0, 0, 0, date('m'), date('d') - rand(3,26),date('Y'));
    
    $this->addHeader($defaultHeaders);
    
    $defaultOptions = array();
    $defaultOptions[CURLOPT_SSL_VERIFYPEER] = FALSE;
    $defaultOptions[CURLOPT_TIMEOUT] = 0;
    //$defaultOptions[CURLOPT_FOLLOWLOCATION] = 1; // por la configuracion de apache servidor
    $defaultOptions[CURLOPT_RETURNTRANSFER] = 1;
    $defaultOptions[CURLINFO_HEADER_OUT] = TRUE;
    $defaultOptions[CURLOPT_HEADER] = FALSE;
    
    $this->addOption($defaultOptions);
    
    $this->n      = $n;
    $this->postVars   = array();
    $this->ch     = curl_init();
  }
  
  /**
   * Destructor
   */
  function __destruct() {
    curl_close($this->ch);
  }
  
  /**
   * Add header
   * @param string $name
   * @param stringe $value
   */
  public function addHeader($name,$value = NULL) {
    if(is_array($name)){
      foreach ($name as $key => $value) {
        $this->headers[$key] = $value;
      }
    }elseif(is_string($name) && !is_null($value)){
      $this->headers[$name] = $value;
    }
  }
  
  /**
   * delete option
   * @param string $name
   */
  public function delHeader($name) {
    if(is_string($name)){
      unset($this->headers[$name]);
    }
  }
  
  /**
   * Add option
   * @param string $name
   * @param stringe $value
   */
  public function addOption($name,$value = NULL) {
    if(is_array($name)){
      foreach ($name as $key => $value) {
        $this->options[$key] = $value;
      }
    }elseif(is_string($name) && !is_null($value)){
      $this->options[$name] = $value;
    }
  }
  
  /**
   * delete option
   * @param string $name
   */
  public function delOption($name) {
    if(is_string($name)){
      unset($this->options[$name]);
    }
  }
  
  /**
   * Add post vars
   * @param string $name
   * @param stringe $value
   */
  public function addPostVar($name,$value = NULL) {
    if(is_array($name)){
      foreach ($name as $key => $value) {
        $this->postVars[$key] = $value;
      }
    }elseif(is_string($name) && !is_null($value)){
      $this->postVars[$name] = $value;
    }
  }
  
  /**
   * delete post vars
   * @param string $name
   */
  public function delPostVar($name) {
    if(is_string($name)){
      unset($this->postVars[$name]);
    }
  }
  
  /**
   * Add get vars
   * @param string $name
   * @param stringe $value
   */
  public function addGetVar($name,$value = NULL) {
    if(is_array($name)){
      foreach ($name as $key => $value) {
        $this->getVars[$key] = $value;
      }
    }elseif(is_string($name) && !is_null($value)){
      $this->getVars[$name] = $value;
    }
  }
  
  /**
   * delete get vars
   * @param string $name
   */
  public function delGetVar($name) {
    if(is_string($name)){
      unset($this->getVars[$name]);
    }
  }
  
  
  /**
   * Execute the request and return the result
   * @param string $url
   * @return string
   */
  public function exec($url) {
    
    // Send the GET vars
    if (sizeof($this->getVars) > 0) {
      $getVars = '';
      foreach($this->getVars as $name => $value) {
        $getVars .= $name.'='.$value.'&';
      }
      $getVars = substr($getVars,0,strlen($getVars)-1);
      if (strpos($getVars,'?') === false) {
        $url .= '?'.$getVars;
      }else{
        $url .= '&'.$getVars;
      }
    }
    
    // Set the options
    curl_setopt ($this->ch, CURLOPT_URL,$url);
    
    curl_setopt ($this->ch, CURLOPT_USERAGENT, $this->headers['agent']);
    curl_setopt ($this->ch, CURLOPT_COOKIEJAR,  $this->headers['cookie']);
    curl_setopt ($this->ch, CURLOPT_COOKIEFILE,  $this->headers['cookie']);
    
    foreach ($this->options as $key => $value) {
      curl_setopt ($this->ch, $key, $value);
    }
    
    // Send the POST vars
    if (sizeof($this->postVars) > 0) {
      $postVars = '';
      foreach($this->postVars as $name => $value) {
        $postVars .= $name.'='.$value.'&';
      }
      $postVars = substr($postVars,0,strlen($postVars)-1);
      
      curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $postVars);
      curl_setopt ($this->ch, CURLOPT_POST, 1);
    }
    
    
    // Execute and retrieve the result
    $t = '';
    while ($t == '') {
      $t = curl_exec($this->ch);
    }
    $this->r_text = $t;
    $this->r_headers = curl_getinfo($this->ch);
    
    return $this->r_text;
  }
  
  /**
   * Return the resulted text
   * @return string
   */
  public function getResult() {
    return $this->r_text;
  }
  
  /**
   * Return the headers
   *
   * @return string
   */
  public function getHeader($variable = NULL) {
    if(!is_null($variable) && isset($this->r_headers[$variable])){
      return $this->r_headers[$variable];
    }
    return $this->r_headers;
  }
}
?>