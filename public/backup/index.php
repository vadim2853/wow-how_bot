<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="./css/date_view.css">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.light_blue-light_green.min.css">
    <script src="//api.bitrix24.com/api/v1/"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>

<div class="text-center invisible" id="loader">
    <div class="spinner-grow text-info" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-info" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<?
require_once (__DIR__ . '/tools.php');

$arProject = executeRest(
    'sonet_group.get',
    array('ORDER' => array('NAME' => 'ASC'), 'IS_ADMIN' => 'N'),
    ($_REQUEST['DOMAIN'] ? $_REQUEST['DOMAIN'] : $_GET['domain']),
    ($_REQUEST['AUTH_ID'] ? $_REQUEST['AUTH_ID'] : $_GET['auth_id'])
);
?>
<form action="./get_result.php" method="post">
    <div class="container" id="container">
        <div class="container demo-card-wide mdl-card mdl-shadow--2dp text-center">
            <label for="from"><h4><?=getMessage('TITLE', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?></h4></label>
            <div>
                <input type="date" name="from" id="from" value="<?=date('Y-m-d')?>"> - <input type="date" name="to" id="to" value="<?=date('Y-m-d')?>">
            </div>
            <br>
            <div class="form-row">
                <div class="input-group mb-3 col-md-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon3"><?=getMessage('PROJECT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?></span>
                    </div>
                    <input type="text" name="project" class="form-control" id="project" aria-describedby="basic-addon3" placeholder="<?=$arProject['result'][0]['NAME']?>" oninput="displayProjects()">
                </div>
            </div>
            <br>
            <div class="mdl-card__actions text-center mdl-card--border">
                <button type='submit' class="mdl-button mdl-js-button mdl-js-ripple-effect" onclick="hide();">
                    <?=getMessage('SUBMIT', ($_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']), __FILE__)?>
                </button>
            </div>
            <input name="domain" value="<?=$_REQUEST['DOMAIN'] ? $_REQUEST['DOMAIN'] : $_GET['domain']?>" type="hidden">
            <input name="auth_id" value="<?=$_REQUEST['AUTH_ID'] ? $_REQUEST['AUTH_ID'] : $_GET['auth_id']?>" type="hidden">
            <input name="lang" value="<?=$_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_GET['lang']?>" type="hidden">
            <input name="first_project" value="<?=$arProject['result'][0]['NAME']?>" type="hidden">
        </div>
    </div>
    <footer class="mdl-mini-footer">
        <div class="row">
            <div class="col-md" align="center">
                <a href="https://primelab.com.ua/" target="_blank"><img src="https://primelab.com.ua/bitrix/templates/aspro-allcorp/images/logo.png" title="PrimeLab" alt="PrimeLab"></a><br>
                <p><br>+38 (099) 636 5 888<br>+38 (097) 680 7 515</p>
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
        <script>
            function hide() {
                $('#container').addClass('invisible');
                $('#loader').removeClass('invisible');
                $('#loader').addClass('visible');
            }

            function displayProjects()
            {
                var options = [];
                var input   = document.getElementById("project").value;

                if (input.length > 2) {
                    setTimeout(1000);
                    BX24.callMethod(
                        'sonet_group.get',
                        {'ORDER': {'NAME': 'ASC'}, 'FILTER': {'%NAME': input}, 'IS_ADMIN': 'N'},
                        function (result)
                        {

                            if (result.error()) {
                                console.error(result.error());
                            } else {

                                if (result.data().length > 0) {
                                    result.data().forEach(function (arProject) {
                                        options.push(arProject.NAME);
                                    })
                                }

                                if (result.more()) {
                                    result.next();
                                }

                            }

                        }

                    );
                    $('#project').autocomplete({source: options});
                } else {
                    $("#project").empty();
                }

            }
        </script>
    </footer>
</form>
</body>
</html>