<?php

require_once ('./tools.php');

if ($_POST['from'] !== '') {
    $inputBeginDate =
        substr($_POST['from'], 0, 4) . '-' .
        substr($_POST['from'], 5, 2) . '-' .
        substr($_POST['from'], 8, 2) . ' 00:00:00';
} else {
    $inputBeginDate = date('Y-m-d') . ' 00:00:00';
}

if ($_POST['to'] !== '') {
    $inputEndDate =
        substr($_POST['to'], 0, 4) . '-' .
        substr($_POST['to'], 5, 2) . '-' .
        substr($_POST['to'], 8, 2) . ' 23:59:59';
} else {
    $inputEndDate = date('Y-m-d') . ' 23:59:59';
}

writeToLog($GLOBALS['logParams'], $_POST);

if (count($_POST['projects']) === 1) {
    $arFilter = array('ID' => $_POST['projects'][0]);
    $arDeal = getInfo($arFilter, $inputBeginDate, $inputEndDate, $_POST['domain'], $_POST['auth_id'], $_POST['lang']);
} elseif (count($_POST['projects']) > 1) {

    foreach ($_POST['projects'] as $index => $projectId) {
        $arFilter = array('ID' => $projectId);
        $arDeal = getInfo($arFilter, $inputBeginDate, $inputEndDate, $_POST['domain'], $_POST['auth_id'], $_POST['lang']);

        if (array_key_exists('TITLE', $arDeal)) {
            $arDeals[$index] = $arDeal;
        }

    }

} else {
    header('Location: https://project-bot.wow-how.com/no_result.php?domain=' . $_POST['domain'] . '&auth_id=' . $_POST['auth_id'] . '&lang=' . $_POST['lang']);
    exit();
}

if (count($_POST['projects']) === 1) {
    ?>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="//api.bitrix24.com/api/v1/"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    </head>
    <body>
    <form action="./index.php">
        <h2><?=getMessage('DEAL_TITLE', $_POST['lang'], __FILE__)?></h2>
        <table class="table">
            <thead>
            <tr>
                <th scope="col"><?=getMessage('DEAL_TABLE_TITLE', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_DATE', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_CUSTOMER', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_STATUS', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_CREATOR', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_RESPONSIBLE', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_AMOUNT', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_HOURS', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_HOUR_COST', $_POST['lang'], __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_EXPENSES', $_POST['lang'], __FILE__)?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?=$arDeal['TITLE']?></td>
                <td><?=$arDeal['DATE']?></td>
                <td><?=$arDeal['CUSTOMER']?></td>
                <td><?=$arDeal['STAGE']?></td>
                <td><?=$arDeal['CREATOR']?></td>
                <td><?=$arDeal['RESPONSIBLE']?></td>
                <td><?=$arDeal['SUM']?></td>
                <td><?=$arDeal['HOURS']?></td>
                <td><?=$arDeal['HOUR_COAST']?></td>
                <td><?=number_format($arDeal['EXPENSES'], 2, '.', ' ')?></td>
            </tr>
            </tbody>
        </table>

        <?if(count($arDeal['INVOICES']) > 0):?>
            <h2><?=getMessage('INVOICES_TITLE', $_POST['lang'], __FILE__)?></h2>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_DATE', $_POST['lang'], __FILE__)?></th>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_STATUS', $_POST['lang'], __FILE__)?></th>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_AMOUNT', $_POST['lang'], __FILE__)?></th>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_CURRENCY', $_POST['lang'], __FILE__)?></th>
                </tr>
                </thead>
                <tbody>

                <?foreach($arDeal['INVOICES'] as $arInvoice):?>
                    <tr>
                        <td><?=$arInvoice['DATE_INSERT']?></td>
                        <td><?=$arInvoice['STATUS']?></td>
                        <td><?=number_format($arInvoice['PRICE'], 2, '.', ' ')?></td>
                        <td><?=$arInvoice['CURRENCY']?></td>
                    </tr>
                <?endforeach;?>

                </tbody>
            </table>
        <?endif;?>

        <?if(count($arDeal['PROJECT']) > 0):?>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col"><?=getMessage('PROJECT_TABLE_TOTAL_TIME', $_POST['lang'], __FILE__)?></th>
                    <th scope="col"><?=getMessage('PROJECT_TABLE_TOTAL_ELAPSED_TIME', $_POST['lang'], __FILE__)?></th>
                    <th scope="col"><?=getMessage('PROJECT_TABLE_PERIOD_ELAPSED_TIME', $_POST['lang'], __FILE__)?></th>
                    <th scope="col"><?=getMessage('PROJECT_DATE', $_POST['lang'], __FILE__)?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$arDeal['PROJECT']['TOTAL_TIME']?></td>
                    <td><?=$arDeal['PROJECT']['TOTAL_ELAPSED_TIME']?></td>
                    <td><?=$arDeal['PROJECT']['PERIOD_ELAPSED_TIME']?></td>
                    <td><?=$arDeal['PROJECT']['DATE']?></td>
                </tr>
                </tbody>
            </table>
        <?endif;?>

        <input name="domain" value="<?=$_POST['domain']?>" type="hidden">
        <input name="auth_id" value="<?=$_POST['auth_id']?>" type="hidden">
        <input name="lang" value="<?=$_POST['lang']?>" type="hidden">
        <br>
        <br>
        <div class="text-center">
            <a class="btn btn-outline-primary text-center btn-lg btn-block" href="https://project-bot.wow-how.com/index.php?domain=<?=$_POST['domain']?>&auth_id=<?=$_POST['auth_id']?>&lang=<?=$_POST['lang']?>"><?=getMessage('BACK', $_POST['lang'], __FILE__)?></a>
        </div>
    </body>
    </form>
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
                minHeight = 1200;
            }

            BX24.resizeWindow(document.body.offsetWidth, 900);

            (function(w,d,u){
                var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
            })(window,document,'https://cdn.bitrix24.ru/b58811/crm/site_button/loader_7_ezyhuz.js');
        </script>
    </footer>
    </body>
    <?
} else {
    ?>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="//api.bitrix24.com/api/v1/"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    </head>
    <body>
    <style>
        .table thead th {
            vertical-align: middle;
            text-align: center;
            width: min-content;
        }

        .dropdown-toggle {
            width: 400px;
        }

        .multiselect-container .dropdown-menu {
            width: 400px;
        }

    </style>
    <form action="./index.php">
        <h2><?=getMessage('DEAL_TITLE', $_POST['lang'], __FILE__)?></h2>
    <table class="table table-sm table-hover">
    <thead>
    <tr>
        <th scope="col"><?=getMessage('DEAL_TABLE_TITLE', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_DATE', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_CUSTOMER', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_STATUS', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_CREATOR', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_RESPONSIBLE', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_AMOUNT', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_HOURS', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_TABLE_HOUR_COST', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('DEAL_EXPENSES', $_POST['lang'], __FILE__)?></th>

        <th scope="col"><?=getMessage('PROJECT_TABLE_TOTAL_TIME', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('PROJECT_TABLE_TOTAL_ELAPSED_TIME', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('PROJECT_TABLE_PERIOD_ELAPSED_TIME', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('PROJECT_DATE', $_POST['lang'], __FILE__)?></th>

        <th scope="col"><?=getMessage('INVOICES_TOTAL_SUM', $_POST['lang'], __FILE__)?></th>
        <th scope="col"><?=getMessage('INVOICES_PAID_TOTAL_SUM', $_POST['lang'], __FILE__)?></th>
    </tr>
    </thead>
    <tbody>
    <?foreach($arDeals as $arDeal):?>
        <tr>
            <td><?=$arDeal['TITLE']?></td>
            <td><?=$arDeal['DATE']?></td>
            <td><?=$arDeal['CUSTOMER']?></td>
            <td><?=$arDeal['STAGE']?></td>
            <td><?=$arDeal['CREATOR']?></td>
            <td><?=$arDeal['RESPONSIBLE']?></td>
            <td><?=$arDeal['SUM']?></td>
            <td><?=$arDeal['HOURS']?></td>
            <td><?=$arDeal['HOUR_COAST']?></td>
            <td><?=number_format($arDeal['EXPENSES'], 2, '.', ' ')?></td>

            <td><?=$arDeal['PROJECT']['TOTAL_TIME']?></td>
            <td><?=$arDeal['PROJECT']['TOTAL_ELAPSED_TIME']?></td>
            <td><?=$arDeal['PROJECT']['PERIOD_ELAPSED_TIME']?></td>
            <td><?=$arDeal['PROJECT']['DATE']?></td>

            <?
            $invoiceTotalSum        = 0;
            $invoicePaidTotalSum    = 0;
            foreach($arDeal['INVOICES'] as $arInvoice) {
                $invoiceTotalSum += $arInvoice['PRICE'];

                if ($arInvoice['STATUS_ID'] === 'P') {
                    $invoicePaidTotalSum += $arInvoice['PRICE'];
                }

            }
            ?>

            <td><?=number_format($invoiceTotalSum, 2, '.', ' ')?></td>
            <td><?=number_format($invoicePaidTotalSum, 2, '.', ' ')?></td>
        </tr>
    <?endforeach;?>
    </tbody>
    </table>
        <input name="domain" value="<?=$_POST['domain']?>" type="hidden">
        <input name="auth_id" value="<?=$_POST['auth_id']?>" type="hidden">
        <input name="lang" value="<?=$_POST['lang']?>" type="hidden">
        <br>
        <br>
        <div class="text-center">
            <a class="btn btn-outline-primary text-center btn-lg btn-block" href="https://project-bot.wow-how.com/index.php?domain=<?=$_POST['domain']?>&auth_id=<?=$_POST['auth_id']?>&lang=<?=$_POST['lang']?>"><?=getMessage('BACK', $_POST['lang'], __FILE__)?></a>
        </div>
    </body>
    </form>
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
                minHeight = 1200;
            }

            BX24.resizeWindow(document.body.offsetWidth, 900);

            (function(w,d,u){
                var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
            })(window,document,'https://cdn.bitrix24.ru/b58811/crm/site_button/loader_7_ezyhuz.js');
        </script>
    </footer>
    </body>
    <?
}
?>