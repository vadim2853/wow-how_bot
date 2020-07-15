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

$arProjects = executeRest(
    'sonet_group.get',
    array(
        'ORDER'     => array('NAME' => 'ASC'),
        'FILTER'    => array('NAME' => ($_POST['project'] ? $_POST['project'] : $_POST['first_project'])),
        'IS_ADMIN'  => 'N'
    ),
    $_POST['domain'],
    $_POST['auth_id']
);
$projectId  = $arProjects['result'][0]['ID'];
$arFilter   = array('>=DATE_CREATE' => $inputBeginDate, '<=DATE_CREATE' => $inputEndDate, 'UF_CRM_PROJECT' => $projectId);
$arDeals    = executeRest(
    'crm.deal.list',
    array(
        'order'     => array('ID' => 'ASC'),
        'filter'    => $arFilter,
        'select'    => array(
            'ID',
            'DATE_CREATE',
            'TITLE',
            'CONTACT_ID',
            'COMPANY_ID',
            'LEAD_ID',
            'STAGE_ID',
            'CREATED_BY_ID',
            'ASSIGNED_BY_ID',
            'OPPORTUNITY',
            'UF_CRM_1549021620'
        )
    ),
    $_POST['domain'],
    $_POST['auth_id']
);

if (count($arDeals['result']) > 0) {
    sleep(1);
    $dealDate = date('d.m.y', strtotime($arDeals['result'][0]['DATE_CREATE']));
    $dealTitle = $arDeals['result'][0]['TITLE'];
    $dealCustomer = '';

    if ($arDeals['result'][0]['CONTACT_ID']) {
        $arContact = executeRest(
            'crm.contact.get',
            array('id' => $arDeals['result'][0]['CONTACT_ID']),
            $_POST['domain'],
            $_POST['auth_id']
        );

        if (strlen($arContact['result']['LAST_NAME']) > 0) {
            $dealCustomer .= $arContact['result']['LAST_NAME'] . ' ';
        }

        if (strlen($arContact['result']['NAME']) > 0) {
            $dealCustomer .= $arContact['result']['NAME'] . ' ';
        }

        if (strlen($arContact['result']['SECOND_NAME']) > 0) {
            $dealCustomer .= $arContact['result']['SECOND_NAME'];
        }

    } elseif ($arDeals['result'][0]['COMPANY_ID']) {
        $arCompany = executeRest(
            'crm.company.get',
            array('id' => $arDeals['result'][0]['COMPANY_ID']),
            $_POST['domain'],
            $_POST['auth_id']
        );
        $dealCustomer = $arCompany['result']['TITLE'];
    } elseif ($arDeals['result'][0]['LEAD_ID']) {
        $arLead = executeRest(
            'crm.lead.get',
            array('id' => $arDeals['result'][0]['LEAD_ID']),
            $_POST['domain'],
            $_POST['auth_id']
        );
        $dealCustomer = $arLead['result']['TITLE'];
    }

    $arStages = executeRest(
        'crm.status.list',
        array('order' => array('NAME' => 'ASC'), 'filter' => array('ENTITY_ID' => 'DEAL_STAGE')),
        $_POST['domain'],
        $_POST['auth_id']
    );

    foreach ($arStages['result'] as $arStage) {

        if ($arStage['STATUS_ID'] === $arDeals['result'][0]['STAGE_ID']) {
            $dealStage = $arStage['NAME'];
            break;
        }

    }

    sleep(1);
    $arUser = executeRest('user.get', array('ID' => $arDeals['result'][0]['CREATED_BY_ID']), $_POST['domain'], $_POST['auth_id']);
    $dealCreator = '';

    if (strlen($arUser['result'][0]['LAST_NAME']) > 0) {
        $dealCreator .= $arUser['result'][0]['LAST_NAME'] . ' ';
    }

    if (strlen($arUser['result'][0]['NAME']) > 0) {
        $dealCreator .= $arUser['result'][0]['NAME'] . ' ';
    }

    if (strlen($arUser['result'][0]['SECOND_NAME']) > 0) {
        $dealCreator .= $arUser['result'][0]['SECOND_NAME'];
    }

    $arUser = executeRest('user.get', array('ID' => $arDeals['result'][0]['ASSIGNED_BY_ID']), $_POST['domain'], $_POST['auth_id']);
    $dealResponsible = '';

    if (strlen($arUser['result'][0]['LAST_NAME']) > 0) {
        $dealResponsible .= $arUser['result'][0]['LAST_NAME'] . ' ';
    }

    if (strlen($arUser['result'][0]['NAME']) > 0) {
        $dealResponsible .= $arUser['result'][0]['NAME'] . ' ';
    }

    if (strlen($arUser['result'][0]['SECOND_NAME']) > 0) {
        $dealResponsible .= $arUser['result'][0]['SECOND_NAME'];
    }

    sleep(1);

    $dealSum            = number_format($arDeals['result'][0]['OPPORTUNITY'], 2, '.', ' ');
    $dealHours          = $arDeals['result'][0]['UF_CRM_1549021620'];
    $dealHourCoast      = number_format(round(($arDeals['result'][0]['OPPORTUNITY'] / $dealHours), 2), 2, '.', ' ');
    $dealInvoices       = array();
    $start              = 0;
    $i                  = 0;
    $arInvoiceStatuses  = executeRest(
        'crm.status.list',
        array('order' => array('NAME' => 'ASC'), 'filter' => array('ENTITY_ID' => 'INVOICE_STATUS')),
        $_POST['domain'],
        $_POST['auth_id']
    );
    sleep(1);

    do {
        $arInvoices = executeRest(
            'crm.invoice.list',
            array(
                'order'     => array('DATE_INSERT' => 'ASC'),
                'filter'    => array('UF_DEAL_ID' => $arDeals['result'][0]['ID']),
                'select'    => array('ID', 'DATE_INSERT', 'STATUS_ID', 'PRICE', 'CURRENCY'),
                'start'     => $start),
            $_POST['domain'],
            $_POST['auth_id']
        );

        if (count($arInvoices['result']) > 0) {

            if (array_key_exists('next', $arInvoices)) {
                $start = $arInvoices['next'];
            }

            if (($start % 100) === 0) {
                sleep(1);
            }

            foreach ($arInvoices['result'] as $arInvoice) {
                $dealInvoices[$i]['DATE_INSERT'] = date('d.m.y', strtotime($arInvoice['DATE_INSERT']));

                foreach ($arInvoiceStatuses['result'] as $arInvoiceStatus) {

                    if ($arInvoiceStatus['STATUS_ID'] === $arInvoice['STATUS_ID']) {
                        $dealInvoices[$i]['STATUS'] = $arInvoiceStatus['NAME'];
                        break;
                    }

                }

                $dealInvoices[$i]['PRICE'] = number_format($arInvoice['PRICE'], 2, '.', ' ');
                $dealInvoices[$i]['CURRENCY'] = $arInvoice['CURRENCY'];
                $i++;
            }

        }

    } while (array_key_exists('next', $arInvoices));

    $dealProject    = array();
    $page           = 1;
    $i              = 0;

    do {
        $arTasks = executeRest(
            'task.item.list',
            array(
                'ORDER'     => array('DATE_START'   => 'ASC'),
                'FILTER'    => array('GROUP_ID'     => $projectId),
                'PARAMS'    => array('NAV_PARAMS'   => array('nPageSize' => 50, 'iNumPage' => $page))
            ),
            $_POST['domain'],
            $_POST['auth_id']
        );

        if (count($arTasks['result']) > 0) {
            sleep(1);

            if (count($arTasks['result']) === 50) {
                $page++;
            }

            foreach ($arTasks['result'] as $arTask) {
                $dealProject['TOTAL_TIME']          += $arTask['TIME_ESTIMATE'];
                $dealProject['TOTAL_ELAPSED_TIME']  += $arTask['TIME_SPENT_IN_LOGS'];
                $page2                              = 1;

                do {
                    $arElapsedTime = executeRest(
                        'task.elapseditem.getlist',
                        array(
                            'TASKID'    => $arTask['ID'],
                            'ORDER'     => array('CREATED_DATE' => 'asc'),
                            'FILTER'    => array(">=CREATED_DATE" => $inputBeginDate, "<=CREATED_DATE" => $inputEndDate),
                            'SELECT'    => array('*'),
                            'PARAMS'    => array('NAV_PARAMS' => array('nPageSize' => 50, 'iNumPage' => $page2))
                        ),
                        $_POST['domain'],
                        $_POST['auth_id']
                    );

                    if (count($arElapsedTime['result']) > 0) {

                        if (($page2 % 2) === 0) {
                            sleep(1);
                        }

                        foreach ($arElapsedTime['result'] as $arTime) {
                            $dealProject['PERIOD_ELAPSED_TIME']  += $arTime['SECONDS'];
                        }

                        if (count($arElapsedTime['result']) === 50) {
                            $page2++;
                        }

                    }

                } while (count($arElapsedTime['result']) === 50);

            }

        }

    } while (count($arTasks['result']) === 50);


    $dealProject['TOTAL_TIME'] =
        floor(($dealProject['TOTAL_TIME'] / 3600)) . ':' .
        sprintf("%'02d", floor(($dealProject['TOTAL_TIME'] % 3600) / 60)) . ':' .
        sprintf("%'02d", floor(($dealProject['TOTAL_TIME'] % 60)));
    $dealProject['TOTAL_ELAPSED_TIME'] =
        floor(($dealProject['TOTAL_ELAPSED_TIME'] / 3600)) . ':' .
        sprintf("%'02d", floor(($dealProject['TOTAL_ELAPSED_TIME'] % 3600) / 60)) . ':' .
        sprintf("%'02d", floor(($dealProject['TOTAL_ELAPSED_TIME'] % 60)));
    $dealProject['PERIOD_ELAPSED_TIME'] =
        floor(($dealProject['PERIOD_ELAPSED_TIME'] / 3600)) . ':' .
        sprintf("%'02d", floor(($dealProject['PERIOD_ELAPSED_TIME'] % 3600) / 60)) . ':' .
        sprintf("%'02d", floor(($dealProject['PERIOD_ELAPSED_TIME'] % 60)));
?>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="//api.bitrix24.com/api/v1/"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    </head>
    <body>
    <form action="./index.php">
        <h2><?=getMessage('DEAL_TITLE', ($_POST['lang']), __FILE__)?></h2>
        <table class="table">
            <thead>
            <tr>
                <th scope="col"><?=getMessage('DEAL_TABLE_TITLE', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_DATE', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_CUSTOMER', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_STATUS', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_CREATOR', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_RESPONSIBLE', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_AMOUNT', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_HOURS', ($_POST['lang']), __FILE__)?></th>
                <th scope="col"><?=getMessage('DEAL_TABLE_HOUR_COST', ($_POST['lang']), __FILE__)?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?=$dealTitle?></td>
                <td><?=$dealDate?></td>
                <td><?=$dealCustomer?></td>
                <td><?=$dealStage?></td>
                <td><?=$dealCreator?></td>
                <td><?=$dealResponsible?></td>
                <td><?=$dealSum?></td>
                <td><?=$dealHours?></td>
                <td><?=$dealHourCoast?></td>
            </tr>
            </tbody>
        </table>

        <?if(count($dealInvoices) > 0):?>
            <h2><?=getMessage('INVOICES_TITLE', ($_POST['lang']), __FILE__)?></h2>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_DATE', ($_POST['lang']), __FILE__)?></th>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_STATUS', ($_POST['lang']), __FILE__)?></th>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_AMOUNT', ($_POST['lang']), __FILE__)?></th>
                    <th scope="col"><?=getMessage('INVOICES_TABLE_CURRENCY', ($_POST['lang']), __FILE__)?></th>
                </tr>
                </thead>
                <tbody>

                <?foreach($dealInvoices as $dealInvoice):?>
                    <tr>
                        <td><?=$dealInvoice['DATE_INSERT']?></td>
                        <td><?=$dealInvoice['STATUS']?></td>
                        <td><?=$dealInvoice['PRICE']?></td>
                        <td><?=$dealInvoice['CURRENCY']?></td>
                    </tr>
                <?endforeach;?>

                </tbody>
            </table>
        <?endif;?>

        <?if(count($dealProject) > 0):?>
            <h2><?=getMessage('PROJECT_TITLE', ($_POST['lang']), __FILE__)?></h2>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col"><?=getMessage('PROJECT_TABLE_TOTAL_TIME', ($_POST['lang']), __FILE__)?></th>
                    <th scope="col"><?=getMessage('PROJECT_TABLE_TOTAL_ELAPSED_TIME', ($_POST['lang']), __FILE__)?></th>
                    <th scope="col"><?=getMessage('PROJECT_TABLE_PERIOD_ELAPSED_TIME', ($_POST['lang']), __FILE__)?></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?=$dealProject['TOTAL_TIME']?></td>
                        <td><?=$dealProject['TOTAL_ELAPSED_TIME']?></td>
                        <td><?=$dealProject['PERIOD_ELAPSED_TIME']?></td>
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
    header('Location: https://project-bot.wow-how.com/no_result.php?domain=' . $_POST['domain'] . '&auth_id=' . $_POST['auth_id'] . '&lang=' . $_POST['lang']);
    exit();
}
?>