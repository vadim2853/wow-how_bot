<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="./css/date_view.css">

    <script src="//api.bitrix24.com/api/v1/"></script>

    <link rel="stylesheet" href="docs/css/bootstrap-3.3.2.min.css" type="text/css">
    <link rel="stylesheet" href="docs/css/bootstrap-example.min.css" type="text/css">
    <link rel="stylesheet" href="docs/css/prettify.min.css" type="text/css">

    <script type="text/javascript" src="docs/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="docs/js/bootstrap-3.3.2.min.js"></script>
    <script type="text/javascript" src="docs/js/prettify.min.js"></script>

    <link rel="stylesheet" href="dist/css/bootstrap-multiselect.css" type="text/css">
    <script type="text/javascript" src="dist/js/bootstrap-multiselect.js"></script>

    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.light_blue-light_green.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
</head>
<body>

<style>
    [type="date"] {
        width: auto;
    }

    [type="checkbox"] {
        width: unset;
    }

    .mdl-mini-footer{
        position: fixed; /* Фиксированное положение */
        left: 0; bottom: 0; /* Левый нижний угол */
        padding: 10px; /* Поля вокруг текста */
        width: 100%;
        background-color: unset;
    }

    hr {
        width: 1140px;
        margin: auto;
        padding-top: inherit;
    }

    #spinner1.mdl-spinner--single-color [class*="mdl-spinner__layer-"] {
        border-color: #acd6df;
    }

    #spinner2.mdl-spinner--single-color [class*="mdl-spinner__layer-"] {
        border-color: #acd6df;
    }

    #spinner3.mdl-spinner--single-color [class*="mdl-spinner__layer-"] {
        border-color: #67bcdc;
    }

</style>

<?php
require_once (__DIR__ . '/tools.php');
?>

<div class="text-center " id="loader">
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading"><?=getMessage('CLOSE_TITLE', $_REQUEST['LANG'], __FILE__)?></h4>
        <p><?=getMessage('CLOSE_BODY', $_REQUEST['LANG'], __FILE__)?></p>
        <hr>
        <p class="mb-0"><?=getMessage('CLOSE_FOOTER', $_REQUEST['LANG'], __FILE__)?></p>
    </div>
</div>
    <div class="mdl-mini-footer text-center">
        <hr class="text-center mx-auto md-auto">
        <footer class="pt-4 my-md-5 pt-md-5 border-top container">
            <div class="row border-top">
                <div class="col-md border-top" align="center">
                    <a href="https://primelab.com.ua/" target="_blank"><img src="https://primelab.com.ua/bitrix/templates/aspro-allcorp/images/logo.png" title="PrimeLab" alt="PrimeLab"></a><br>
                    <small class="d-block mb-3 text-muted">+38 (099) 636 5 888<br>+38 (097) 680 7 515</small>
                </div>
            </div>
            <script data-skip-moving="true">
                BX24.resizeWindow(document.body.offsetWidth, 800);

                (function(w,d,u){
                    var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                    var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
                })(window,document,'https://cdn.bitrix24.ru/b58811/crm/site_button/loader_7_ezyhuz.js');

            </script>
        </footer>
    </div>
</body>
</html>