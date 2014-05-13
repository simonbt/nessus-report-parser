<?php
/**
 * ReportGenerator -- vulnerabilities.php
 * User: Simon Beattie @si_bt
 * Date: 15/04/2014
 * Time: 09:39
 */

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<link rel="stylesheet" type="text/css" href="vulnerabilities.css">';
echo '</head>';

echo "<table border=0 cellpadding=0 cellspacing=0>
";

require_once(__DIR__ . "/../config.php");

$OSList = array(
    "Windows"   =>  "Microsoft Windows",
    "FreeBSD"   =>  "FreeBSD",
    "Linux"     =>  "Linux"
);

$reportId = $_GET['reportid'];
$severity = $_GET['severity'];


$reportData = json_decode(getReportData($reportId, $severity, $url)); //Get all report data from the API. Returns JSON so decoding that too

if (!$reportData)
{
    die("There is no data to display, try adjusting your severity settings");
}

outputVulnHostPort($reportData, $OSList); // Picking out only the Vulnerabilities and each host, protocol and port from the full data.


function outputVulnHostPort($reportData) // Pass full report array to return hosts, ports and protocols sorted by vulnerability
{
    $data = array();
    foreach ($reportData as $hostData)
    {

        $potentialOperatingSystems = explode(PHP_EOL, $hostData->OS);
        $OS = trim(array_shift($potentialOperatingSystems));

        if ($hostData->fqdn == "")
        {
            $name = $hostData->netbios;
        } else {
            $name = $hostData->fqdn;
        }

        if (!$name)
        {
            $name = "Unable to accurately identify";
        }

        foreach ($hostData->vulnerabilities as $vulnerability)
        {
            $data[] = array(
                                                'ip' => ip2long($hostData->hostname),
                                                'name' => $name,
                                                'os' => $OS,
                                                'vuln' => $vulnerability->name,
                                                'risk' => $vulnerability->risk,
                                                'severity' => $vulnerability->severity);
        }

    }

    usort($data, function($firstArrayElement, $secondArrayElement)
    {
        $first = $firstArrayElement['ip'];
        $second = $secondArrayElement['ip'];

        $ret = strcmp($first, $second);
        if($ret == 0)
        {
            $firstSeverity = (float) $firstArrayElement['severity'];
            $secondSeverity = (float) $secondArrayElement['severity'];

            if($secondSeverity > $firstSeverity)
            {
                return 1;
            }
            elseif($firstSeverity > $secondSeverity)
            {
                return -1;
            }
            elseif($firstSeverity == $secondSeverity)
            {
                return 0;
            }
        }

        return $ret;
    });

    $ip = "";


    $counts = array();
    foreach ($data as $value){
        foreach ($value as $key2 => $value2){
            if ($key2 == "ip")
            {
                $index = $value2;
                if (array_key_exists($index, $counts)){
                    $counts[$index]++;
                } else {
                    $counts[$index] = 1;
                }
            }
        }
    }


    foreach ($data as $vuln)
    {
        if ($vuln['risk'] == "Medium" )
        {
            $colour = "orange";
        } else {
            $colour = "red";
        }
        if ($ip == long2ip($vuln['ip']))
        {
            print( "
            <tr>
                  <td class=" . $colour .">" . $vuln['vuln'] . "</td>
                  <td class=" . $colour .">" . $vuln['risk'] . "</td>
                  <td class=" . $colour .">" . $vuln['severity'] . "</td>
             </tr>
            ");
        } else {
            print( "
            <tr>
                  <td border:solid 1pt gray; vertical-align: top; rowspan=\"" . $counts[$vuln['ip']] . "\">" . long2ip($vuln['ip']) . "</td>
                  <td border:solid 1pt gray; vertical-align: top; rowspan=\"" . $counts[$vuln['ip']] . "\">" . $vuln['name'] . "</td>
                  <td border:solid 1pt gray; vertical-align: top; rowspan=\"" . $counts[$vuln['ip']] . "\">" . $vuln['os'] . "</td>
                  <td class=" . $colour .">" . $vuln['vuln'] . "</td>
                  <td class=" . $colour .">" . $vuln['risk'] . "</td>
                  <td class=" . $colour .">" . $vuln['severity'] . "</td>
             </tr>

            ");

            $ip = long2ip($vuln['ip']);
        }

    }
}

echo " </table>";

function getReportData($reportId, $severity, $url) // Pass reportID, severity and $url from config file to return full report JSON
{
    $query = '?report=2&reportid=' . $reportId . '&severity=' . $severity;
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

