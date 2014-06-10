<?php
/**
 * ReportGenerator -- output.php
 * User: Simon Beattie @si_bt
 * Date: 15/04/2014
 * Time: 11:24
 */

require_once(__DIR__ . "/config.php");
include_once(__DIR__ . '/Library/Files.php');
$files = new \Library\files();
$openDLPDirecory = __DIR__ . '/opendlpUploads';
date_default_timezone_set('Europe/London');


echo '  <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link rel="stylesheet" type="text/css" href="reports/main.css">
                <title>RandomStorm Report Generator</title>
                <script language="javascript">
                    var popupWindow = null;
                    function centeredPopup(url,winName,w,h,scroll){
                        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
                        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
                        settings =
                        \'height=\'+h+\',width=\'+w+\',top=\'+TopPosition+\',left=\'+LeftPosition+\',scrollbars=\'+scroll+\',resizable\';
                        popupWindow = window.open(url,winName,settings)
                    }
';
echo "
                    function loadingScreen(){
                        document.body.innerHTML += '<div class=\"loading\">Loading&#8230;</div>';
                    }
                </script>
            </head>
";

echo '<div class="menu">';
echo '<div><a href="openDLP.php" onclick="loadingScreen()"><img src="images/logo.png" alt="RandomStorm Limited" /></a></div>';
echo '<div>
<br>
<b>Options</b>
<br>
Your severity setting is: ' . $severity . '<br>
<p><a class="myButton"; href="index.php" onclick="loadingScreen()">Nessus Reports</a>
<p><a class="myButton"; href="files.php" onclick="loadingScreen()">File Management</a>
<p><b>Imported Reports</b>
';

$openDLPList = array_slice(scandir($openDLPDirecory),2);
$openDLPArray = array();
foreach ( $openDLPList as $fileName )
{
    $openDLPArray[$fileName] = $files->encodeName($fileName);
}



if (!$openDLPArray)
{
    echo "There are no reports available on the system<br>";
} else {

    foreach ($openDLPArray as $name => $hash) {
        echo '<p><b>' . $name . '</b> (' . date ("d F Y H:i:s",filemtime($openDLPDirecory .'/' . $name)) . ') - ';
        echo '
<label>
    <select onchange="location = this.options[this.selectedIndex].value; loadingScreen()">
        <option selected> Select Report </option>
        <option value="reports/opendlp.php?filename=' . $name . '">OpenDLP Overview Report</option>
    </select>
</label>

            ';
    }
echo '</div>';
}

function curlGet($url, $query, $auth)
{

    $url_final = $url . '' . $query;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url_final);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, "$auth[username]:$auth[password]");

    $return = curl_exec($ch);
    curl_close($ch);
    return $return;



}
