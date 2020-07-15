<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

<?

require_once (__DIR__ . '/tools.php');

?>

<div class="alert alert-danger" role="alert">
    <h4 class="alert-heading"><?=getMessage('TITLE', $_REQUEST['LANG'], __FILE__)?></h4>
    <p><?=getMessage('BODY', $_REQUEST['LANG'], __FILE__)?></p>
    <hr>
    <p class="mb-0"><?=getMessage('FOOTER', $_REQUEST['LANG'], __FILE__)?></p>
</div>
<footer class="pt-4 my-md-5 pt-md-5 border-top container">
    <div class="row">
        <div class="col-md" align="center">
            <a href="https://primelab.com.ua/"><img src="https://primelab.com.ua/bitrix/templates/aspro-allcorp/images/logo.png" title="PrimeLab" alt="PrimeLab"></a>
            <small class="d-block mb-3 text-muted">+38 (099) 636 5 888<br>+38 (097) 680 7 515</small>
        </div>
    </div>
</footer>
<script data-skip-moving="true">
    (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn.bitrix24.ru/b58811/crm/site_button/loader_7_ezyhuz.js');
</script>
</body>