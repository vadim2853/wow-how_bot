<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <script src="//api.bitrix24.com/api/v1/dev/"></script>
</head>
<?

include(__DIR__ . '/tools.php');
$placementOptions   = json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);

$curl = curl_init("https://" . $_REQUEST['DOMAIN'] . "/rest/user.current.json");
curl_setopt_array($curl, array(
        CURLOPT_POST            => 1,
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_POSTFIELDS      => http_build_query(array("auth" => $_REQUEST['AUTH_ID'])))
);
$curlExec = curl_exec($curl);
curl_close($curl);
$arUser = json_decode($curlExec, true);
$userId = $arUser['result']['ID'];

writeToLog($GLOBALS['logParams'], array($placementOptions, $arUser));
$arDeal             = executeRest(
    'crm.deal.get',
    array('id' => $placementOptions['ENTITY_VALUE_ID']),
    $_REQUEST['DOMAIN'],
    $_REQUEST['AUTH_ID'],
    $userId
);
$projectId  = $arDeal['result']['UF_CRM_PROJECT'];
$arGroups   = executeRest(
    'sonet_group.get',
    array('ORDER' => array('NAME' => 'ASC'), 'FILTER' => array('ID' => $projectId), 'IS_ADMIN' => 'Y'),
    $_REQUEST['DOMAIN'],
    $_REQUEST['AUTH_ID'],
    $userId
);
$projectName = null;

if ($projectId) {

    foreach ($arGroups['result'] as $arGroup) {

        if ($arGroup['ID'] == $projectId) {
            $projectName = $arGroup['NAME'];
            break;
        }

    }

}

if ($placementOptions['MODE'] == 'edit') {

?>
<body>
<div class="crm-entity-widget-content-block-inner">
    <div class="crm-entity-widget-content-block-field-container">
        <div class="crm-entity-widget-content-block-field-container-inner">
            <div class="crm-entity-widget-content-inner-row">
                <div class="crm-entity-widget-content-search-row crm-entity-widget-content-block-textreset crm-entity-widget-content-block-complete">
                    <div class="crm-entity-widget-content-search-inner">
                        <div class="crm-entity-widget-content-search-box">
                            <div class="crm-entity-widget-img-box crm-entity-widget-img-company"></div>

                            <?

                            if ($projectName) {
                                echo '<input type="text" id="project" list="project" class="crm-entity-widget-content-input crm-entity-widget-content-search-input" oninput="displayProjects()" placeholder = "' . $projectName . '">';
                            } else {
                                echo '<input type="text" id="project" list="project" class="crm-entity-widget-content-input crm-entity-widget-content-search-input" oninput="displayProjects()">';
                            }

                            ?>

                        </div>
                        <div class="crm-entity-widget-btn-close" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?

} else {
    echo '<body style="background: #f9fafb;">';
    echo '<div class="crm-entity-widget-content-block-inner"><div class="crm-entity-widget-content-block-inner-text">' . $projectName . '</div></div>';
}

?>

<script>
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

                    }

                }

            );
            $('#project').autocomplete({source: options});
        } else {
            $("#project").empty();
        }

    }

    $("#project").on("autocompleteselect", function( event, ui ) {
        BX24.callMethod(
            'sonet_group.get',
            {'ORDER': {'NAME': 'ASC'}, 'FILTER': {'%NAME': ui.item.value}, 'IS_ADMIN': 'N'},
            function (result) {

                if (result.error()) {
                    console.error(result.error());
                } else {

                    if (result.data().length > 0) {
                        var projectList = result.data();
                        BX24.placement.call('setValue', projectList[0].ID);
                    }

                }

            }
        );
    });

</script>
</body>