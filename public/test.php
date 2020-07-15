<?

define('WEBHOOK', 'https://wow-how.bitrix24.ua/rest/573/1xa0k2sde5rthp85/');

define('LOG_PATH', __DIR__ . '/log/');
$logParams = array("write_log" => true, "max_count_file_log" => 5);

function getLogs($path) {
    $cortex = opendir($path);
    $arLogs = array();

    while($file = readdir($cortex)) {
        $info = new SplFileInfo($file);
        $type = $info->getExtension();

        if ($type == "log" && $file != "." && $file != "..") {

            if (file_exists($path . $file)) {
                $ctime = filectime($path . $file) . ',' . $file;
                $arLogs[$ctime] = $file;
            }

        }

    }

    closedir($cortex);
    asort($arLogs);
    return $arLogs;
}

function writeToLog($log_write_params, $info = array()) {

    if(!$log_write_params["write_log"]) {
        return false;
    }

    $logFolder = substr(LOG_PATH, 0, -1);

    if(!is_dir($logFolder)) {
        mkdir($logFolder);
    }

    $arListFile = getLogs(LOG_PATH);
    $size = count($arListFile);

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

function incomingWebhook ($type, array $params = array()) {
    $url = WEBHOOK . $type . '.json';
    $curl = curl_init();
    $data = http_build_query($params);
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER  => 0,
        CURLOPT_POST            => 1,
        CURLOPT_HEADER          => 0,
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_URL             => $url,
        CURLOPT_POSTFIELDS      => $data
    ));
    $curlExec = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($curlExec, true);
    return $result;
}

$arResult = incomingWebhook('crm.deal.get', array('id' => 115));

echo '<pre>';
print_r($arResult);
echo '</pre>';