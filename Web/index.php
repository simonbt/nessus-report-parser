<?php
/**
 * ReportGenerator -- output.php
 * User: Simon Beattie @si_bt
 * Date: 15/04/2014
 * Time: 11:24
 */

require_once(__DIR__ . "/config.php");



echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<link rel="stylesheet" type="text/css" href="reports/main.css">';
echo '<title>RandomStorm Report Generator</title>';
echo "
<script language=\"javascript\">
var popupWindow = null;
function centeredPopup(url,winName,w,h,scroll){
    LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
    TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
    settings =
        'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
popupWindow = window.open(url,winName,settings)
}
</script>
";

echo '</head>';




echo '<div class="menu">';
echo '<div><img src="images/logo.png" alt="RandomStorm Limited" /></div>';
echo '<div>
<br>
<b>Options</b>
<br>
Your severity setting is: ' . $severity . ' <i>set in config.php</i><br>
<p><a class="myButton"; href="Library/severity.html" onclick="centeredPopup(this.href,\'myWindow\',\'500\',\'300\',\'yes\');return false">Change Severity</a>
<a class="myButton"; href="#">View Descriptions</a>
<a class="myButton"; href="#">Download xls format</a></p>
<b>Reports</b><br><br>
';


$reports = json_decode(getReportList($url));

if (!$reports)
{
    echo "There are no reports available on the system<br>";
} else {
    foreach ($reports as $report) {
        echo $report->report_name . ' - ' . $report->created . '<br>';
        echo '
            <p><a class="myButton"; href="reports/hosts.php?reportid=' . $report->id . '&severity=' . $severity . '">Host View</a>
            <a class="myButton"; href="reports/vulnerabilities.php?reportid=' . $report->id . '&severity=' . $severity . '">Vulnerability View</a>
            <a class="myButton"; href="reports/descriptions.php?reportid=' . $report->id . '&severity=' . $severity . '">Description View</a></p>
            ';
    }
echo '</div>';
}
function getReportList($url)
{
    $query = '?listreports=1';
    $report = curlGet($url, $query);
    return $report;
}


function curlGet($url, $query)
{
    $url_final = $url . '' . $query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_final);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}