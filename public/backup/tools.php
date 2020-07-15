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