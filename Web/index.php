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
echo '</head>';
echo '<div class="menu">';
echo '<div><img src="images/logo.png" alt="RandomStorm Limited" /></div>';

echo '<div>
<br>
<b>Imported Reports</b>
<br>
Your severity setting is: ' . $severity . ' <i>set in config.php</i><br><br>
';


$reports = json_decode(getReportList($url));

if (!$reports)
{
    echo "There are no reports available on the system<br>";
    echo "To import a report, run import.php [Report file name] from the program directory";
}
foreach ($reports as $report) {
    echo 'ID: ' . $report->id . '<br>';
    echo 'Name: ' . $report->report_name . '<br>';
    echo 'Created: ' . $report->created . '<br>';
    echo '<a href="reports/hosts.php?reportid=' . $report->id . '&severity=' . $severity . '">View the hosts output</a><br>';
    echo '<a href="reports/vulnerabilities.php?reportid=' . $report->id . '&severity=' . $severity . '">View the vulnerability output</a><br>';
    echo '<a href="reports/descriptions.php?reportid=' . $report->id . '&severity=' . $severity . '">View the vulnerability descriptions</a><br><br>';
}

echo '</div>';

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