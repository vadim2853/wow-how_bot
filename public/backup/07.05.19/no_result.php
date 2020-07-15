<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="./css/date_view.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.light_blue-light_green.min.css">
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <script src="//api.bitrix24.com/api/v1/"></script>
    <title>Installation</title>
</head>
<body>
<?
require_once (__DIR__ . '/tools.php');
?>

<div class="container" id="container">
    <div class="jumbotron text-center">
        <h1 class="display-4"><?=getMessage('TITLE', $_GET['lang'], __FILE__)?></h1>
        <p class="lead"><?=getMessage('BODY', $_GET['lang'], __FILE__)?></p>
        <a class="btn btn-primary btn-lg" href="https://project-bot.wow-how.com/index.php?domain=<?=$_GET['domain']?>&auth_id=<?=$_GET['auth_id']?>&lang=<?=$_GET['lang']?>" role="button"><?=getMessage('BACK', $_GET['lang'], __FILE__)?></a>
    </div>
</div>
<input name="domain" value="<?=$_GET['domain']?>" type="hidden">
<input name="auth_id" value="<?=$_GET['auth_id']?>" type="hidden">
<input name="lang" value="<?=$_GET['lang']?>" type="hidden">
<footer class="pt-4 my-md-5 pt-md-5 border-top container">
    <div class="row">
        <div class="col-md" align="center">
            <a href="https://primelab.com.ua/" target="_blank"><img src="https://primelab.com.ua/bitrix/templates/aspro-allcorp/images/logo.png" title="PrimeLab" alt="PrimeLab"></a>
            <small class="d-block mb-3 text-muted">+38 (099) 636 5 888<br>+38 (097) 680 7 515</small>
        </div>
    </div>
    <script data-skip-moving="true">

        var currentSize = BX24.getScrollSize();
        var minHeight = currentSize.scrollHeight;

        if (minHeight < 400) {
            minHeight = 100;
        }

        BX24.resizeWindow(document.body.offsetWidth, minHeight);

        (function(w,d,u){
            var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
            var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn.bitrix24.ru/b58811/crm/site_button/loader_7_ezyhuz.js');
    </script>
</footer>
</body>
</html>