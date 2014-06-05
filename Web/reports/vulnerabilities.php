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
echo '<link rel="stylesheet" type="text/css" href="main.css">';
echo '<title>Vulnerability View</title>';
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

function loadingScreen(){
   document.body.innerHTML += '<div class=\"loading\">Loading&#8230;</div>';
}
</script>
";
echo '</head>';
echo "<a onclick='loadingScreen()' class=\"myButton\"; href=\"../index.php\">Return to Menu</a></p>";
echo "<table border=0 cellpadding=0 cellspacing=0>";

require_once(__DIR__ . "/../config.php");

$reportId = $_GET['reportid'];
$severity = $_GET['severity'];


$reportData = json_decode(getReportData($reportId, $severity, $url, $auth)); //Get all report data from the API. Returns JSON so decoding that too

if (!$reportData)
{
    die("There is no data to display, try adjusting your severity settings");
}

outputVulnHostPort($reportData); // Picking out only the Vulnerabilities and each host, protocol and port from the full data.


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
        if (($vuln['risk'] == "High") || ($vuln['risk'] == "Critical"))
        {
            $colour = "red";
        }
        elseif ($vuln['risk'] == "Medium")
        {
            $colour = "orange";
        }
        elseif ($vuln['risk'] == "Low")
        {
            $colour = "green";
        }
        elseif ($vuln['risk'] == "Info")
        {
            $colour = "blue";
        }
        else
        {
            $colour = "blue";
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

echo "</table>";

function getReportData($reportId, $severity, $url, $auth) // Pass reportID, severity and $url from config file to return full report JSON
{
    $query = '?report=2&reportid=' . $reportId . '&severity=' . $severity;
    $report = curlGet($url, $query, $auth);
    return $report;
}


function curlGet($url, $query, $auth) // Curl function
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
