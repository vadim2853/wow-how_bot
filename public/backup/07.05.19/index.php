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

<div class="text-center invisible" id="loader">
    <div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active" id="spinner1"></div>
    <div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active" id="spinner2"></div>
    <div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active" id="spinner3"></div>
</div>

<?
require_once (__DIR__ . '/tools.php');
?>
<form action="./get_result.php" method="post">
    <div class="container text-center" id="container">
            <label for="from"><h4><?=getMessage('TITLE', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?></h4></label>
            <div>
                <input type="date" name="from" id="from" value="<?=date('Y-m-d')?>"> - <input type="date" name="to" id="to" value="<?=date('Y-m-d')?>">
            </div>
            <br>
            <select id="example-dropUp" multiple="multiple" name="projects[]">
                <option></option>
            </select>
            <br>
            <br>
                <button type='submit' class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" onclick="hide();">
                    <?=getMessage('SUBMIT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>
                </button>
            <input name="domain" value="<?=$_REQUEST['DOMAIN'] ? $_REQUEST['DOMAIN'] : $_GET['domain']?>" type="hidden">
            <input name="auth_id" value="<?=$_REQUEST['AUTH_ID'] ? $_REQUEST['AUTH_ID'] : $_GET['auth_id']?>" type="hidden">
            <input name="lang" value="<?=$_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']?>" type="hidden">
            <input id="select_all_text" value="<?=getMessage('SELECT_ALL_TEXT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>" type="hidden">
            <input id="filter_placeholder" value="<?=getMessage('FILTER_PLACEHOLDER', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>" type="hidden">
            <input id="non_selected_text" value="<?=getMessage('NON_SELECTED_TEXT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>" type="hidden">
            <input id="n_selected_text" value="<?=getMessage('N_SELECTED_TEXT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>" type="hidden">
            <input id="all_selected_text" value="<?=getMessage('ALL_SELECTED_TEXT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>" type="hidden">

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

            var currentSize = BX24.getScrollSize();
            var minHeight = currentSize.scrollHeight;

            if (minHeight < 400) {
                minHeight = 100;
            }

            BX24.resizeWindow(document.body.offsetWidth, 800);

            (function(w,d,u){
                var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
            })(window,document,'https://cdn.bitrix24.ru/b58811/crm/site_button/loader_7_ezyhuz.js');
        </script>
        <script>
            function hide() {
                $('#container').addClass('invisible');
                $('#loader').removeClass('invisible');
                $('#loader').addClass('visible');
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
                    BX24.callMethod(
                        'sonet_group.get',
                        {'ORDER': {'NAME': 'ASC'}, 'FILTER': {'%NAME': input}, 'IS_ADMIN': 'N'},
                        function (result) {

                            if (result.error()) {
                                console.error(result.error());
                            } else {

                                if (result.data().length > 0) {
                                    $('#example-dropUp').find('option').remove();
                                    var x = document.getElementById("example-dropUp");
                                    result.data().forEach(function (arProject) {
                                        $('#example-dropUp').append('<option value="' + arProject.ID + '">' + arProject.NAME + '</option>');
                                    });

                                    $('#example-dropUp').multiselect('rebuild');
                                    var arInputs = document.getElementsByClassName("multiselect-search");
                                    arInputs[0].value = input;
                                    //arInputs[0].focus();
                                }
                                var x = document.getElementById("example-dropUp");
                                console.log(x);

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