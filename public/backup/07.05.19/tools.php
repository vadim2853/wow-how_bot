<?php

/**
 * Log-files folder path
 */
define("LOG_PATH", __DIR__ . '/log/');

/**
 * @var array $logParams indicates whether to write log-files or not and how many days to store them
 */
$logParams = array("write_log" => true, "max_count_file_log" => 14);

/**
 * Function to getting list of logs
 *
 * @param string $path  Path to log-files
 *
 * @return array        List of log-files
 * @author              Minko Alexander
 */
function getLogs($path)
{
    $cortex = opendir($path);
    $arLogs = array();

    while($file = readdir($cortex)) {
        $info = new SplFileInfo($file);
        $type = $info->getExtension();

        if ($type == "log" && $file != "." && $file != "..") {

            if (file_exists($path . $file)) {
                $ctime          = filectime($path . $file) . ',' . $file;
                $arLogs[$ctime] = $file;
            }

        }

    }

    closedir($cortex);
    asort($arLogs);
    return $arLogs;
}

/**
 * Log-file writing function
 *
 * @param   array   $log_write_params   Log-file writing options
 * @param   array   $info               Information that will be written to a log-file
 *
 * @return  boolean                     Log-file recording flag
 * @author                              Minko Alexander
 */
function writeToLog($log_write_params, $info = array())
{

    if(!$log_write_params["write_log"]) {
        return false;
    }

    $logFolder = substr(LOG_PATH, 0, -1);

    if(!is_dir($logFolder)) {
        mkdir($logFolder);
    }

    $arListFile = getLogs(LOG_PATH);
    $size       = count($arListFile);

    if($log_write_params["max_count_file_log"] && $size > $log_write_params["max_count_file_log"]) {
        array_splice($arListFile, $size - $log_write_params["max_count_file_log"]);

        foreach ($arListFile as $name) {
            unlink(LOG_PATH . $name);
        }

    }

    file_put_contents(
        LOG_PATH . 'log_'.date("Y_m_d").'.log',
        print_r($info, 1),
        FILE_APPEND
    );
    file_put_contents(
        LOG_PATH . 'log_'.date("Y_m_d").'.log',
        "\n_________________________________\n\n",
        FILE_APPEND
    );
    chmod(LOG_PATH . 'log_'.date("Y_m_d").'.log', 0777);
    return true;
}

/**
 * Function for executing B24 API-method
 *
 * @param   string  $method         API-method to be executed
 * @param   array   $params         Parameters for API-method
 * @param   string  $domain         Portal address
 * @param   string  $accessToken    Token to access the portal
 *
 * @return  array                   Result of the executing API-method
 * @author                          Minko Alexander
 */
function executeRest($method, $params = array(), $domain, $accessToken)
{
    $curl = curl_init("https://" . $domain . "/rest/" . $method. ".json");
    curl_setopt_array($curl, array(
        CURLOPT_POST            => 1,
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_POSTFIELDS      => http_build_query(array_merge($params, array("auth" => $accessToken))))
    );
    $curlExec = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($curlExec, true);
    writeToLog($GLOBALS['logParams'], array(
        'INFO'      => 'Результат выполнения запроса ' . $method,
        'PARAMS'    => $params,
        'RESULT'    => $result)
    );

    if (array_key_exists('error', $result)) {
        exit();
    }

    return $result;
}

/**
 * @param string    $code       Needed phrase code
 * @param string    $language   Needed phrase language
 * @param string    $file       From which file function is called
 *
 * @return string               Needed phrase
 * @author                      Minko Alexander
 */
function getMessage ($code, $language, $file)
{
    $path       = '';
    $fileName   = basename($file);

    if ($language == 'ua') {
        $path = __DIR__ . '/lang/ua/';
        require ($path . $fileName);
        return $MESS[$code];

    } elseif ($language == 'ru') {
        $path = __DIR__ . '/lang/ru/';
        require ($path . $fileName);
        return $MESS[$code];
    } else {
        $path = __DIR__ . '/lang/en/';
        require ($path . $fileName);
        return $MESS[$code];
    }

}

function getInfo ($arFilter, $beginDate, $endDate, $domain, $token, $lang)
{
    $arProjects = executeRest(
        'sonet_group.get',
        array(
            'ORDER'     => array('NAME' => 'ASC'),
            'FILTER'    => $arFilter,
            'IS_ADMIN'  => 'N'
        ),
        $domain,
        $token
    );

    if (count($arProjects['result']) === 1) {
        $arProject = $arProjects['result'][0];
    } else {
        $arProject = $arProjects['result'];
    }

    $projectId                  = $arProject['ID'];
    $arDeal['PROJECT']['DATE']  =
        substr($arProject['DATE_CREATE'], 8, 2) . '.' .
        substr($arProject['DATE_CREATE'], 5, 2) . '.' .
        substr($arProject['DATE_CREATE'], 2, 2);
    $arDeals = executeRest(
        'crm.deal.list',
        array(
            'order'     => array('ID' => 'ASC'),
            'filter'    => array(
                '>=DATE_CREATE'     => $beginDate,
                '<=DATE_CREATE'     => $endDate,
                'UF_CRM_PROJECT'    => $projectId
            ),
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
                'UF_CRM_1554889136',
                'UF_CRM_1554297194',
                'UF_CRM_1549021620'
            )
        ),
        $domain,
        $token
    );

    if (count($arDeals['result']) > 0) {
        $arDeal['EXPENSES'] = 0;

        foreach ($arDeals['result'][0]['UF_CRM_1554297194'] as $strExpenses) {
            $arDeal['EXPENSES'] += floatval($strExpenses);
        }

        sleep(1);
        $arDeal['DATE']     = date('d.m.y', strtotime($arDeals['result'][0]['DATE_CREATE']));
        $arDeal['TITLE']    = $arDeals['result'][0]['TITLE'];
        $arDeal['CUSTOMER'] = '';

        if ($arDeals['result'][0]['CONTACT_ID']) {
            $arContact = executeRest(
                'crm.contact.get',
                array('id' => $arDeals['result'][0]['CONTACT_ID']),
                $domain,
                $token
            );

            if (strlen($arContact['result']['LAST_NAME']) > 0) {
                $arDeal['CUSTOMER'] .= $arContact['result']['LAST_NAME'] . ' ';
            }

            if (strlen($arContact['result']['NAME']) > 0) {
                $arDeal['CUSTOMER'] .= $arContact['result']['NAME'] . ' ';
            }

            if (strlen($arContact['result']['SECOND_NAME']) > 0) {
                $arDeal['CUSTOMER'] .= $arContact['result']['SECOND_NAME'];
            }

        } elseif ($arDeals['result'][0]['COMPANY_ID']) {
            $arCompany = executeRest(
                'crm.company.get',
                array('id' => $arDeals['result'][0]['COMPANY_ID']),
                $domain,
                $token
            );
            $arDeal['CUSTOMER'] = $arCompany['result']['TITLE'];
        } elseif ($arDeals['result'][0]['LEAD_ID']) {
            $arLead = executeRest(
                'crm.lead.get',
                array('id' => $arDeals['result'][0]['LEAD_ID']),
                $domain,
                $token
            );
            $arDeal['CUSTOMER'] = $arLead['result']['TITLE'];
        }

        $arStages = executeRest(
            'crm.status.list',
            array('order' => array('NAME' => 'ASC'), 'filter' => array('ENTITY_ID' => 'DEAL_STAGE')),
            $domain,
            $token
        );

        foreach ($arStages['result'] as $arStage) {

            if ($arStage['STATUS_ID'] === $arDeals['result'][0]['STAGE_ID']) {
                $arDeal['STAGE'] = $arStage['NAME'];
                break;
            }

        }

        sleep(1);
        $arUser = executeRest('user.get', array('ID' => $arDeals['result'][0]['CREATED_BY_ID']), $domain, $token);
        $arDeal['CREATOR'] = '';

        if (strlen($arUser['result'][0]['LAST_NAME']) > 0) {
            $arDeal['CREATOR'] .= $arUser['result'][0]['LAST_NAME'] . ' ';
        }

        if (strlen($arUser['result'][0]['NAME']) > 0) {
            $arDeal['CREATOR'] .= $arUser['result'][0]['NAME'] . ' ';
        }

        if (strlen($arUser['result'][0]['SECOND_NAME']) > 0) {
            $arDeal['CREATOR'] .= $arUser['result'][0]['SECOND_NAME'];
        }

        $arUser = executeRest('user.get', array('ID' => $arDeals['result'][0]['ASSIGNED_BY_ID']), $domain, $token);
        $arDeal['RESPONSIBLE'] = '';

        if (strlen($arUser['result'][0]['LAST_NAME']) > 0) {
            $arDeal['RESPONSIBLE'] .= $arUser['result'][0]['LAST_NAME'] . ' ';
        }

        if (strlen($arUser['result'][0]['NAME']) > 0) {
            $arDeal['RESPONSIBLE'] .= $arUser['result'][0]['NAME'] . ' ';
        }

        if (strlen($arUser['result'][0]['SECOND_NAME']) > 0) {
            $arDeal['RESPONSIBLE'] .= $arUser['result'][0]['SECOND_NAME'];
        }

        sleep(1);
        $arDeal['SUM']          = number_format($arDeals['result'][0]['OPPORTUNITY'], 2, '.', ' ');
        $arDeal['HOURS']        = $arDeals['result'][0]['UF_CRM_1554889136'];
        $arDeal['HOUR_COAST']   = number_format($arDeals['result'][0]['UF_CRM_1549021620'], 2, '.', ' ');
        $start                  = 0;
        $i                      = 0;
        $arInvoiceStatuses      = executeRest(
            'crm.status.list',
            array('order' => array('NAME' => 'ASC'), 'filter' => array('ENTITY_ID' => 'INVOICE_STATUS')),
            $domain,
            $token
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
                $domain,
                $token
            );

            if (count($arInvoices['result']) > 0) {

                if (array_key_exists('next', $arInvoices)) {
                    $start = $arInvoices['next'];
                }

                if (($start % 100) === 0) {
                    sleep(1);
                }

                foreach ($arInvoices['result'] as $arInvoice) {
                    $arDeal['INVOICES'][$i]['DATE_INSERT'] = date('d.m.y', strtotime($arInvoice['DATE_INSERT']));

                    foreach ($arInvoiceStatuses['result'] as $arInvoiceStatus) {

                        if ($arInvoiceStatus['STATUS_ID'] === $arInvoice['STATUS_ID']) {
                            $arDeal['INVOICES'][$i]['STATUS']       = $arInvoiceStatus['NAME'];
                            $arDeal['INVOICES'][$i]['STATUS_ID']    = $arInvoiceStatus['STATUS_ID'];
                            //!!!---------!!!
                            break;
                        }

                    }

                    $arDeal['INVOICES'][$i]['PRICE']    = $arInvoice['PRICE'];
                    $arDeal['INVOICES'][$i]['CURRENCY'] = $arInvoice['CURRENCY'];
                    $i++;
                }

            }

        } while (array_key_exists('next', $arInvoices));

        $page   = 1;
        $page2  = 1;

        do {
            $arTasks = executeRest(
                'task.item.list',
                array(
                    'ORDER'     => array('DATE_START'   => 'ASC'),
                    'FILTER'    => array('GROUP_ID'     => $projectId),
                    'PARAMS'    => array('NAV_PARAMS'   => array('nPageSize' => 50, 'iNumPage' => $page))
                ),
                $domain,
                $token
            );

            if (count($arTasks['result']) > 0) {
                sleep(1);

                if (count($arTasks['result']) === 50) {
                    $page++;
                }

                foreach ($arTasks['result'] as $arTask) {
                    $arDeal['PROJECT']['TOTAL_TIME']            += $arTask['TIME_ESTIMATE'];
                    $arDeal['PROJECT']['TOTAL_ELAPSED_TIME']    += $arTask['TIME_SPENT_IN_LOGS'];
                    $page                                       = 1;

                    do {
                        $arElapsedTime = executeRest(
                            'task.elapseditem.getlist',
                            array(
                                'TASKID'    => $arTask['ID'],
                                'ORDER'     => array('CREATED_DATE' => 'asc'),
                                'FILTER'    => array(">=CREATED_DATE" => $beginDate, "<=CREATED_DATE" => $endDate),
                                'SELECT'    => array('*'),
                                'PARAMS'    => array('NAV_PARAMS' => array('nPageSize' => 50, 'iNumPage' => $page2))
                            ),
                            $domain,
                            $token
                        );

                        if (count($arElapsedTime['result']) > 0) {

                            if (($page2 % 2) === 0) {
                                sleep(1);
                            }

                            foreach ($arElapsedTime['result'] as $arTime) {
                                $arDeal['PROJECT']['PERIOD_ELAPSED_TIME']  += $arTime['SECONDS'];
                            }

                            if (count($arElapsedTime['result']) === 50) {
                                $page2++;
                            }

                        }

                    } while (count($arElapsedTime['result']) === 50);

                }

            }

        } while (count($arTasks['result']) === 50);

        $arDeal['PROJECT']['TOTAL_TIME'] =
            floor(($arDeal['PROJECT']['TOTAL_TIME'] / 3600)) . ':' .
            sprintf("%'02d", floor(($arDeal['PROJECT']['TOTAL_TIME'] % 3600) / 60)) . ':' .
            sprintf("%'02d", floor(($arDeal['PROJECT']['TOTAL_TIME'] % 60)));
        $arDeal['PROJECT']['TOTAL_ELAPSED_TIME'] =
            floor(($arDeal['PROJECT']['TOTAL_ELAPSED_TIME'] / 3600)) . ':' .
            sprintf("%'02d", floor(($arDeal['PROJECT']['TOTAL_ELAPSED_TIME'] % 3600) / 60)) . ':' .
            sprintf("%'02d", floor(($arDeal['PROJECT']['TOTAL_ELAPSED_TIME'] % 60)));
        $arDeal['PROJECT']['PERIOD_ELAPSED_TIME'] =
            floor(($arDeal['PROJECT']['PERIOD_ELAPSED_TIME'] / 3600)) . ':' .
            sprintf("%'02d", floor(($arDeal['PROJECT']['PERIOD_ELAPSED_TIME'] % 3600) / 60)) . ':' .
            sprintf("%'02d", floor(($arDeal['PROJECT']['PERIOD_ELAPSED_TIME'] % 60)));
    }

    return $arDeal;
}