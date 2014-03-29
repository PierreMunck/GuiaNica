<?
include '../lib/Curl.php';

function nicalog($data){
    $log = print_r($data,TRUE);
    $logfile = '/var/www/guianica.com/log/login- '.date('[d-M-Y-h-i-s]').'.log';
    $fp = @fopen($logfile, 'a+');
    @fwrite($fp, $log);
    @fclose($fp);
}

if(isset($_POST['token'])){
    
    $token = $_POST['token'];
    $url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='.$token;
    
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = '';
    $result = curl_exec($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    
    if($headers['http_code'] == 200){
        $info = json_decode($result);
        if(isset($info->name)){
            echo 'bienvenido '.$info->name;
            die();
        }
    }
}
echo 'error';
?>