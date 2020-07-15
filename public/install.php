<head>
    <meta charset="UTF-8">
    <script src="//api.bitrix24.com/api/v1/"></script>
    <title>Installation</title>
</head>
<body>
<script>
    BX24.init(function () {
        BX24.installFinish();
    });
</script>
</body>
<?php

require_once (__DIR__ . '/config.php');
require_once (__DIR__ . '/tools.php');
/*$dsn        = 'mysql:dbname=' . $arDbInfo['DATABASE'] . ';host=' . $arDbInfo['HOST'];
try {
    $connection = new PDO($dsn, $arDbInfo['USER'], $arDbInfo['PASSWORD']);
} catch (PDOException $e) {
    writeToLog($GLOBALS['logParams'], array($e->getMessage()));
}

$connection->query('SET NAMES utf8');
$curl   = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER  => 0,
    CURLOPT_POST            => 1,
    CURLOPT_HEADER          => 0,
    CURLOPT_RETURNTRANSFER  => 1,
    CURLOPT_URL             => 'https://' . $_REQUEST['DOMAIN'] . '/rest/app.info?auth=' . $_REQUEST['AUTH_ID'])
);
$curlExec = curl_exec($curl);
curl_close($curl);
$arAppInfo  = json_decode($curlExec, true);
$tariff     = '';

switch (substr($arAppInfo['result']['LICENSE'], 3)) {
    case 'project':
        $tariff = 'Проект';
        break;
    case 'tf':
        $tariff = 'Проект+';
        break;
    case 'team':
        $tariff = 'Команда';
        break;
    case 'company':
        $tariff = 'Компания';
        break;
    case 'demo':
        $tariff = 'Демо-режим';
        break;
    case 'nfr':
        $tariff = 'NFR-лицензия';
        break;
    case 'selfhosted':
        $tariff = 'Коробка';
        break;
}

$curl   = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER  => 0,
    CURLOPT_POST            => 1,
    CURLOPT_HEADER          => 0,
    CURLOPT_RETURNTRANSFER  => 1,
    CURLOPT_URL             => 'https://' . $_REQUEST['DOMAIN'] . '/rest/user.current?auth=' . $_REQUEST['AUTH_ID'])
);
$curlExec = curl_exec($curl);
curl_close($curl);
$arUserInfo = json_decode($curlExec, true);
$lastName   = trim($arUserInfo['result']['LAST_NAME']);
$name       = trim($arUserInfo['result']['NAME']);
$secondName = trim($arUserInfo['result']['SECOND_NAME']);
$query      = $connection->prepare('SELECT * FROM clients WHERE DOMAIN = ?');
$query->execute(array($_REQUEST['DOMAIN']));
$arResult   = $query->fetchAll();
$date       = date('Y-d-m H:i:s');

if (empty($arResult)) {
    $query      = $connection->prepare('INSERT INTO clients VALUES ("", ?, ?, ?, ?, ?, ?, "", "WOW HOW", ?, ?, ?, ?)');
    $arParams   = array(
        $_REQUEST['DOMAIN'],
        date('c'),
        $tariff,
        $lastName,
        $arUserInfo['result']['EMAIL'],
        ($arUserInfo['result']['WORK_PHONE'] ? $arUserInfo['result']['WORK_PHONE'] : $arUserInfo['result']['PERSONAL_PHONE']),
        $arUserInfo['result']['WORK_POSITION'],
        $arUserInfo['result']['WORK_COMPANY'],
        $name,
        $secondName
    );
    $query->execute($arParams);
}
sleep(1);*/
/*$userField  = array(
    'USER_TYPE_ID'  => 'PROJECT',
    'HANDLER'       => 'https://project-bot.wow-how.com/handler.php',
    'TITLE'         => 'Проект',
    'DESCRIPTION'   => 'Проект'
);
executeRest('userfieldtype.add', $userField, $_REQUEST['DOMAIN'], $_REQUEST['AUTH_ID']);*/
executeRest('userfieldtype.list', array(), $_REQUEST['DOMAIN'], $_REQUEST['AUTH_ID']);

executeRest(
    "crm.deal.userfield.add",
    array("FIELDS" => array(
        "USER_TYPE_ID"      => "project",
        "FIELD_NAME"        => "PROJECT",
        "EDIT_FORM_LABEL"   => "Проект",
        "LIST_COLUMN_LABEL" => "Проект",
        "EDIT_IN_LIST"      => "Y",
        "SHOW_IN_LIST"      => "Y",
        "SHOW_FILTER"       => "Y")
    ),
    $_REQUEST["DOMAIN"],
    $_REQUEST["AUTH_ID"]
);

?>