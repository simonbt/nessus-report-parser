<?php
/**
 * ReportGenerator -- host.php
 * User: Simon Beattie @si_bt
 * Date: 15/04/2014
 * Time: 09:39
 */

require_once(__DIR__ . "/../config.php");

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<link rel="stylesheet" type="text/css" href="main.css">';
echo '<title>Report Host View</title>';
echo '</head>';
echo "<a class=\"myButton\"; href=\"../index.php\">Return to Menu</a></p>";


$reportId = $_GET['reportid'];
$severity = $_GET['severity']; //Dealing with GET requests, setting $reportid and $severity variables

$reportData = json_decode(getReportData($reportId, $severity, $url)); //Get all report data from the API. Returns JSON so decoding that too

if (!$reportData)
{
    die("There is no data to display, try adjusting your severity settings");
}

hostReport($reportData); // Picking out only the Vulnerabilities and each host, protocol and port from the full data.



function hostReport($reportData) // Pass full report array to return hosts, ports and protocols sorted by vulnerability
{

    foreach ($reportData as $vulnerability) {
        echo '<div class="headers"><br>' . $vulnerability[0][0]->vulnerability . '</div>'; // Output Vulnerability name
        echo "<table>";
        $loop = 0;
        foreach ($vulnerability[1] as $hostObj) {

            if ($loop == 0) {
                $loop++;
                print('<tr>');
            }

            print('
                    <td>' . $hostObj->host_id . '</td>
                    <td>' . strtoupper($hostObj->protocol) . '/' .  $hostObj->port . '</td>
                    ');
            $loop++;
            if ($loop == 5) {
                print('</tr>');
                $loop = 0;
                continue;
            }

        }


        echo "</tr>";
        echo "</table>";
    }

}



function getReportData($reportId, $severity, $url) // Pass reportID, severity and $url from config file to return full report JSON
{
    $query = '?report=1&reportid=' . $reportId . '&severity=' . $severity;
    $report = curlGet($url, $query);
    return $report;
}


function curlGet($url, $query) // Curl function
{
    $url_final = $url . '' . $query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_final);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}

