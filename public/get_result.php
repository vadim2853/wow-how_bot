<?php

set_time_limit(0);
//error_reporting(E_ALL);
//error_log(error_get_last(), 3, __DIR__ . "/reports/errors.txt");

require_once ('./tools.php');
require_once ('./config.php');

$filename = __DIR__ . '/reports/flag.txt';

if (file_exists($filename)) {
    @header('Location: https://project-bot.wow-how.com/late.php?LANG=' . $_REQUEST['LANG']);
    writeToLog($GLOBALS['logParams'], array('ERROR1' => error_get_last()));
    exit();
} else {
    $handle = fopen($filename, "w");

    if (false === $handle) {
        @header('Location: https://project-bot.wow-how.com/error.php?LANG=' . $_REQUEST['LANG']);
        writeToLog($GLOBALS['logParams'], array('ERROR2' => error_get_last()));
        exit();
    }

}

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

writeToLog($GLOBALS['logParams'], array('POST' => $_POST));

if ((count($_POST['projects']) === 1) && $_POST['projects'][0]) {
    $arFilter = array('ID' => $_POST['projects'][0]);
    $arDeal = getInfo($arFilter, $inputBeginDate, $inputEndDate, $_REQUEST['DOMAIN'], $_REQUEST['AUTH_ID'], $_REQUEST['LANG'], $_REQUEST['USER_ID']);
} elseif (count($_POST['projects']) > 1) {

    foreach ($_POST['projects'] as $index => $projectId) {
        $arFilter = array('ID' => $projectId);
        $arDeal = getInfo($arFilter, $inputBeginDate, $inputEndDate, $_REQUEST['DOMAIN'], $_REQUEST['AUTH_ID'], $_REQUEST['LANG'], $_REQUEST['USER_ID']);

        if (array_key_exists('TITLE', $arDeal)) {
            $arDeals[$index] = $arDeal;
        }

    }

} else {
    $start = 0;

    do {
        $arProjects = executeRest(
            'sonet_group.get',
            array('ORDER' => array('NAME' => 'ASC'), 'IS_ADMIN' => 'Y', 'start' => $start),
            $_REQUEST['DOMAIN'],
            $_REQUEST['AUTH_ID'],
            $_REQUEST['USER_ID']
        );

        if (count($arProjects['result']) > 0) {

            foreach ($arProjects['result'] as $arProject) {
                $arFilter = array('ID' => $arProject['ID']);
                /*$duration = microtime(true) - $start;

                if (floor($duration/60) > 45) {
                    $curl = curl_init("https://oauth.bitrix.info/oauth/token/?grant_type=refresh_token&client_id=local.5c98f2ed6d3c52.41098832&client_secret=T2XnGnwxYckvcvcpTW74FZ5xKGRK3YzrDnfwXQ5C4ct3Y267Ju&refresh_token=" . $_REQUEST['REFRESH_ID']);
                    curl_setopt_array($curl, array(
                        CURLOPT_POST            => 1,
                        CURLOPT_RETURNTRANSFER  => 1,
                        CURLOPT_POSTFIELDS      => http_build_query(array()))
                    );
                    $curlExec = curl_exec($curl);
                    curl_close($curl);
                    $arResult = json_decode($curlExec, true);

                    if (array_key_exists('error', $arResult)) {
                        unlink($filename);
                        writeToLog($GLOBALS['logParams'], array(
                            'INFO'      => 'Ошибка получения нового токена',
                            'RESULT'    => $arResult)
                        );
                        exit();
                    }

                    $_REQUEST['AUTH_ID']    = $arResult['access_token'];
                    $_REQUEST['REFRESH_ID'] = $arResult['refresh_token'];
                    $start                  = microtime(true);
                }*/

                $arDeal2 = getDeal($arProject, $inputBeginDate, $inputEndDate, $_REQUEST['DOMAIN'], $_REQUEST['AUTH_ID'], $_REQUEST['REFRESH_ID'], $_REQUEST['USER_ID']);

                if (array_key_exists('TITLE', $arDeal2)) {
                    $arDeals[] = $arDeal2;
                }

            }

            if (array_key_exists('next', $arProjects)) {
                $start = $arProjects['next'];
            }

        }

    } while (array_key_exists('next', $arProjects));

}

if ((count($arDeals) == 0) && (count($arDeal) == 0)) {
    unlink($filename);
    writeToLog($GLOBALS['logParams'], array(
            'INFO'      => 'Нет сделок',
            'RESULT'    => $arResult)
    );
    @header('Location: https://project-bot.wow-how.com/no_result.php?LANG=' . $_REQUEST['LANG']);
    exit();
}

$fileName   = 'report_' . date('dmY', strtotime($inputBeginDate)) . '_' . date('dmY', strtotime($inputEndDate)) . '_' . date('His') . '.csv';
$fp         = fopen(__DIR__ . '/reports/' . $fileName, 'w');

if ((count($_POST['projects']) === 1) && $_POST['projects'][0]) {
    //----------begin----------
    fputcsv($fp, array(iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TITLE', $_REQUEST['LANG'], __FILE__))), ';');
    fputcsv($fp, array(), ';');
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_TITLE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_DATE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_CUSTOMER', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_STATUS', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_CREATOR', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_RESPONSIBLE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_AMOUNT', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_HOURS', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_HOUR_COST', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_EXPENSES', $_REQUEST['LANG'], __FILE__));
    fputcsv($fp, $arTitle, ';');
    $arDealCsv[] = $arDeal['TITLE'];
    $arDealCsv[] = $arDeal['DATE'];
    $arDealCsv[] = $arDeal['CUSTOMER'];
    $arDealCsv[] = $arDeal['STAGE'];
    $arDealCsv[] = $arDeal['CREATOR'];
    $arDealCsv[] = $arDeal['RESPONSIBLE'];
    $arDealCsv[] = $arDeal['SUM'];
    $arDealCsv[] = $arDeal['HOURS'];
    $arDealCsv[] = $arDeal['HOUR_COAST'];
    $arDealCsv[] = number_format($arDeal['EXPENSES'], 2, '.', ' ');
    fputcsv($fp, $arDealCsv, ';');

    if (count($arDeal['INVOICES']) > 0) {
        fputcsv($fp, array(), ';');
        fputcsv($fp, array(iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_TITLE', $_REQUEST['LANG'], __FILE__))), ';');
        fputcsv($fp, array(), ';');
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_TABLE_DATE', $_REQUEST['LANG'], __FILE__));
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_TABLE_STATUS', $_REQUEST['LANG'], __FILE__));
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_TABLE_AMOUNT', $_REQUEST['LANG'], __FILE__));
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_TABLE_CURRENCY', $_REQUEST['LANG'], __FILE__));
        fputcsv($fp, $arTitle, ';');

        foreach ($arDeal['INVOICES'] as $arInvoice) {
            $arInvoiceCsv   = array();
            $arInvoiceCsv[] = $arInvoice['DATE_INSERT'];
            $arInvoiceCsv[] = $arInvoice['STATUS'];
            $arInvoiceCsv[] = number_format($arInvoice['PRICE'], 2, '.', ' ');
            $arInvoiceCsv[] = $arInvoice['CURRENCY'];
            fputcsv($fp, $arInvoiceCsv, ';');
        }

    }

    if (count($arDeal['PROJECT']) > 0) {
        fputcsv($fp, array(), ';');
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_TABLE_TOTAL_TIME', $_REQUEST['LANG'], __FILE__));
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_TABLE_TOTAL_ELAPSED_TIME', $_REQUEST['LANG'], __FILE__));
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_TABLE_PERIOD_ELAPSED_TIME', $_REQUEST['LANG'], __FILE__));
        $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_DATE', $_REQUEST['LANG'], __FILE__));
        fputcsv($fp, $arTitle, ';');
        $arProjectCsv   = array();
        $arProjectCsv[] = $arDeal['PROJECT']['TOTAL_TIME'];
        $arProjectCsv[] = $arDeal['PROJECT']['TOTAL_ELAPSED_TIME'];
        $arProjectCsv[] = $arDeal['PROJECT']['PERIOD_ELAPSED_TIME'];
        $arProjectCsv[] = $arDeal['PROJECT']['DATE'];
        fputcsv($fp, $arProjectCsv, ';');
    }

} else {
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_TITLE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_DATE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_CUSTOMER', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_STATUS', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_CREATOR', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_RESPONSIBLE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_AMOUNT', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_HOURS', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_TABLE_HOUR_COST', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('DEAL_EXPENSES', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_TABLE_TOTAL_TIME', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_TABLE_TOTAL_ELAPSED_TIME', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_TABLE_PERIOD_ELAPSED_TIME', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('PROJECT_DATE', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_TOTAL_SUM', $_REQUEST['LANG'], __FILE__));
    $arTitle[] = iconv('UTF-8', 'Windows-1251', getMessage('INVOICES_PAID_TOTAL_SUM', $_REQUEST['LANG'], __FILE__));
    fputcsv($fp, $arTitle, ';');

    foreach ($arDeals as $arDeal) {
        //writeToLog($GLOBALS['logParams'], $arDeal);
        $arDealCsv              = array();
        $arDealCsv[]            = $arDeal['TITLE'];
        $arDealCsv[]            = $arDeal['DATE'];
        $arDealCsv[]            = iconv('UTF-8', 'Windows-1251', $arDeal['CUSTOMER']);
        $arDealCsv[]            = iconv('UTF-8', 'Windows-1251', $arDeal['STAGE']);
        $arDealCsv[]            = $arDeal['CREATOR'];
        $arDealCsv[]            = $arDeal['RESPONSIBLE'];
        $arDealCsv[]            = $arDeal['SUM'];
        $arDealCsv[]            = $arDeal['HOURS'];
        $arDealCsv[]            = $arDeal['HOUR_COAST'];
        $arDealCsv[]            = number_format($arDeal['EXPENSES'], 2, '.', ' ');
        $arDealCsv[]            = $arDeal['PROJECT']['TOTAL_TIME'];
        $arDealCsv[]            = $arDeal['PROJECT']['TOTAL_ELAPSED_TIME'];
        $arDealCsv[]            = $arDeal['PROJECT']['PERIOD_ELAPSED_TIME'];
        $arDealCsv[]            = $arDeal['PROJECT']['DATE'];
        $invoiceTotalSum        = 0;
        $invoicePaidTotalSum    = 0;

        if (array_key_exists('INVOICES', $arDeal)) {

            foreach($arDeal['INVOICES'] as $arInvoice) {
                $invoiceTotalSum += $arInvoice['PRICE'];

                if ($arInvoice['STATUS_ID'] === 'P') {
                    $invoicePaidTotalSum += $arInvoice['PRICE'];
                }

            }

        }

        $arDealCsv[]            = number_format($invoiceTotalSum, 2, '.', ' ');
        $arDealCsv[]            = number_format($invoicePaidTotalSum, 2, '.', ' ');
        //writeToLog($GLOBALS['logParams'], $arDealCsv);
        fputcsv($fp, $arDealCsv, ';');
    }
    writeToLog($GLOBALS['logParams'], array('END2'));
}
writeToLog($GLOBALS['logParams'], array('END3'));
fclose($fp);
writeToLog($GLOBALS['logParams'], array('END4'));
$arProjects = executeRest(
    'im.notify',
    array('to' => $_REQUEST['USER_ID'], 'message' => '[url=' . REPORTS_PATH . $fileName . ']Отчет по проектам[/url] доступен для скачивания', 'type' => 'SYSTEM'),
    $_REQUEST['DOMAIN'],
    $_REQUEST['AUTH_ID'],
    $_REQUEST['USER_ID']
);
writeToLog($GLOBALS['logParams'], array('END'));
unlink($filename);
@header('Location: https://project-bot.wow-how.com/index.php?DOMAIN=' . $_REQUEST['DOMAIN'] . '&AUTH_ID=' . $_REQUEST['AUTH_ID'] . '&LANG=' . $_REQUEST['LANG']);
exit();