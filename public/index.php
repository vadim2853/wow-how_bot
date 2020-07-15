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

<?
require_once (__DIR__ . '/tools.php');

if (!array_key_exists('USER_ID', $_REQUEST)) {
    $url        = 'https://' . $_REQUEST['DOMAIN'] . '/rest/user.current?auth=' . $_REQUEST['AUTH_ID'];
    $curl       = curl_init();
    curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_POST            => 1,
            CURLOPT_HEADER          => 0,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_URL             => $url)
    );
    $curlExec   = curl_exec($curl);
    curl_close($curl);
    $arUserInfo = json_decode($curlExec, true);
    $userId     = $arUserInfo['result']['ID'];
}

?>
<form action="./get_result.php" method="post" name="data">
    <div class="container text-center" id="container">
            <label for="from"><h4><?=getMessage('TITLE', $_REQUEST['LANG'], __FILE__)?></h4></label>
            <div>
                <input type="date" name="from" id="from" value="<?=date('Y-m-d')?>"> - <input type="date" name="to" id="to" value="<?=date('Y-m-d')?>">
            </div>
            <br>
            <select id="example-dropUp" multiple="multiple" name="projects[]">
                <option></option>
            </select>
            <br>
            <br>
                <button type='button' class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" onclick="hide();">
                    <?=getMessage('SUBMIT', $_REQUEST['LANG'], __FILE__)?>
                </button>
            <input name="DOMAIN" value="<?=$_REQUEST['DOMAIN']?>" type="hidden">
            <input name="AUTH_ID" value="<?=$_REQUEST['AUTH_ID']?>" type="hidden">
            <input name="REFRESH_ID" value="<?=$_REQUEST['REFRESH_ID']?>" type="hidden">
            <input name="LANG" value="<?=$_REQUEST['LANG']?>" type="hidden">
            <input name="USER_ID" value="<?=($_REQUEST['USER_ID'] ? $_REQUEST['USER_ID'] : $userId)?>" type="hidden">
            <input id="select_all_text" value="<?=getMessage('SELECT_ALL_TEXT', $_REQUEST['LANG'], __FILE__)?>" type="hidden">
            <input id="filter_placeholder" value="<?=getMessage('FILTER_PLACEHOLDER', $_REQUEST['LANG'], __FILE__)?>" type="hidden">
            <input id="non_selected_text" value="<?=getMessage('NON_SELECTED_TEXT', $_REQUEST['LANG'], __FILE__)?>" type="hidden">
            <input id="n_selected_text" value="<?=getMessage('N_SELECTED_TEXT', $_REQUEST['LANG'], __FILE__)?>" type="hidden">
            <input id="all_selected_text" value="<?=getMessage('ALL_SELECTED_TEXT', $_REQUEST['LANG'], __FILE__)?>" type="hidden">

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

            function hide() {
                /*$('#container').addClass('invisible');
                $('#loader').removeClass('invisible');
                $('#loader').addClass('visible');*/
                var domain  = document.getElementsByName('DOMAIN');
                var auth_id = document.getElementsByName('AUTH_ID');
                var lang    = document.getElementsByName('LANG');
                location = 'https://project-bot.wow-how.com/dummy.php?DOMAIN=' + domain[0].value + '&AUTH_ID=' + auth_id[0].value + '&LANG=' + lang[0].value;
                var formData    = new FormData(document.forms.data);
                var xhr         = new XMLHttpRequest();
                xhr.open("POST", "get_result.php");
                xhr.send(formData);
            }

            $(document).ready(function() {

                $('#example-dropUp').multiselect({
                    enableFiltering         : true,
                    includeSelectAllOption  : true,
                    maxHeight               : 400,
                    dropUp                  : false,
                    selectAllText           : document.getElementById("select_all_text").value,
                    filterPlaceholder       : document.getElementById("filter_placeholder").value,
                    nonSelectedText         : document.getElementById("non_selected_text").value,
                    nSelectedText           : document.getElementById("n_selected_text").value,
                    allSelectedText         : document.getElementById("all_selected_text").value,
                });

            });

            function displayProjects()
            {
                var obSelect = document.getElementsByClassName("multiselect-search");
                var input = obSelect[0].value;

                if (input.length > 2) {
                    $('#example-dropUp').find('option').remove();
                    BX24.callMethod(
                        'sonet_group.get',
                        {'ORDER': {'NAME': 'ASC'}, 'FILTER': {'%NAME': input}, 'IS_ADMIN': 'Y'},
                        function (result) {

                            if (result.error()) {
                                console.error(result.error());
                            } else {

                                if (result.data().length > 0) {
                                    var x = document.getElementById("example-dropUp");
                                    result.data().forEach(function (arProject) {
                                        $('#example-dropUp').append('<option value="' + arProject.ID + '">' + arProject.NAME + '</option>');
                                    });

                                    $('#example-dropUp').multiselect('rebuild');
                                    var arInputs = document.getElementsByClassName("multiselect-search");
                                    arInputs[0].value = input;
                                    //arInputs[0].focus();
                                }

                                if (result.more()) {
                                    result.next();
                                }

                            }

                        }
                    );
                }

            }
        </script>
    </footer>
    </div>
</form>
</body>
</html>