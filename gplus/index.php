<?php
/*include_once('lib/parser/Rss.php');

$parser = new Parserurl();
$parser->setUrl("http://experiencias.folkano.com");
$parser->parse();
$parser->setDescription(__DIR__."/desc/experiencias_folkano_com.desc");
$result = $parser->getInfo();
print_r($result);*/


?>

<html>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
        
        <script type="text/javascript">
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/client:plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
            function signinCallback(authResult) {
                if (authResult['access_token']) {
                    // Autorizado correctamente
                    // Oculta el botón de inicio de sesión ahora que el usuario está autorizado, por ejemplo:
                    //document.getElementById('signinButton').setAttribute('style', 'display: none');
                    var directgoogle = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&token=' +authResult['access_token'];
                    var viaguia = '/gplus/auth.php';
                    
                    console.log(authResult['access_token']);
                    jQuery.post( viaguia,
                        {
                            token : authResult['access_token']
                        },
                        function(data) {
                            alert(data);
                        }
                    );
                } else if (authResult['error']) {
                    console.log(authResult);
                    // Se ha producido un error.
                    // Posibles códigos de error:
                    //   "access_denied": el usuario ha denegado el acceso a la aplicación.
                    //   "immediate_failed": no se ha podido dar acceso al usuario de forma automática.
                    // console.log('There was an error: ' + authResult['error']);
                }
            }
        </script>
    </head>
    <body>
        
    </body>
</html>

<span id="signinButton">
  <span
    class="g-signin"
    data-callback="signinCallback"
    data-clientid="811607084789-54qnd1158cnon6ciisggruadlghr86m1.apps.googleusercontent.com"
    data-cookiepolicy="single_host_origin"
    data-requestvisibleactions="http://schemas.google.com/AddActivity"
    data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile"
    data-login_via="/gplus/auth.php"
    >
  </span>
</span>

