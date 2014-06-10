<?php
/**
 * slim -- ReportTemplates.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 16:36
 */

namespace Library;


class ReportTemplates
{

    public function hosts($reportData)
    {

        echo '
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/main.css">
<title>Report Host View</title>

                <script language=\"javascript\">

                function loadingScreen(){
   document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
                }
                </script>

</head>
<a onclick="loadingScreen()" class="myButton" href="/">Return to Menu</a></p>
';

        foreach ($reportData as $vulnerability) {
            echo '

<div class="headers"><br> ' . $vulnerability[0][0]['vulnerability'] . ' </div>
<table>

';

            $loop = 0;
            foreach ($vulnerability[1] as $hostObj) {

                if ($loop == 0) {
                    $loop++;
                    print('<tr>');
                }

                echo '
                    <td>' . $hostObj['host_id'] . '</td>
                    <td>' . strtoupper($hostObj['protocol']) . '/' . $hostObj['port'] . '</td>
                    ';
                $loop++;
                if ($loop == 5) {
                    echo '</tr>';
                    $loop = 0;
                    continue;
                }

            }


            echo '</tr>';
            echo '</table>';
        }
    }

    public function pci($reportData)
    {


        echo '

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/main.css">
<title>PCI View</title>
<script language=\"javascript\">


function loadingScreen(){
   document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
}
</script>
</head>

<a onclick="loadingScreen()" class="myButton" href="/">Return to Menu</a></p>
<table border=0 cellpadding=0 cellspacing=0>

';


        $ignoredInfos = array(
            "Service Detection",
            "Nessus SYN scanner",
            "Web-Server Allows Password Auto-Completion",
            "IPSEC Internet Key Exchange (IKE) Version 1 Detection",
        );

        $tidyNames = array(
            "ssh"        => "Secure Shell Protocol",
            "dns"        => "Domain Name Service",
            "ftp"        => "File Transfer Protocol",
            "mysql"      => "MySQL Database",
            "smtp"       => "Simple Mail Transfer Protocol",
            "http"       => "Hypertext Transfer Protocol",
            "subversion" => "Subversion Version Manager",
            "pptp"       => "Point-to-Point Tunneling Protocol",
            "www"        => "World Wide Web",
            "savant"     => "Savant",
            "pop3"       => "Post Office Protocol v3",
            "imap"       => "Internet Message Access Protocol"
        );

        $risks = array(
            "Critical" => "CRITICAL",
            "High"     => "HIGH",
            "Medium"   => "MEDIUM",
            "Low"      => "LOW",
            "None"     => "INFO"
        );

        $data = array();
        foreach ($reportData as $hostData) {
            foreach ($hostData['vulnerabilities'] as $vulnerability) {

                if (array_key_exists($vulnerability['risk'], $risks)) {
                    $risk = $risks[$vulnerability['risk']];
                } else {
                    $risk = $vulnerability['risk'];
                }

                $data[] = array(
                    'ip'       => ip2long($hostData['hostname']),
                    'vuln'     => $vulnerability['name'],
                    'risk'     => $risk,
                    'severity' => $vulnerability['severity'],
                    'port'     => strtoupper($vulnerability['protocol']) . "/" . $vulnerability['port'],
                    'service'  => $vulnerability['service']
                );
            }

        }


        usort($data, function ($firstArrayElement, $secondArrayElement) {
            $first = $firstArrayElement['ip'];
            $second = $secondArrayElement['ip'];

            $ret = strcmp($first, $second);
            if ($ret == 0) {
                $firstSeverity = (float)$firstArrayElement['severity'];
                $secondSeverity = (float)$secondArrayElement['severity'];

                if ($secondSeverity > $firstSeverity) {
                    return 1;
                } elseif ($firstSeverity > $secondSeverity) {
                    return -1;
                } elseif ($firstSeverity == $secondSeverity) {
                    return 0;
                }
            }

            return $ret;
        });

        $ip = "";


        $counts = array();
        foreach ($data as $value) {
            foreach ($value as $key2 => $value2) {
                if ($key2 == "ip") {
                    $index = $value2;
                    if (array_key_exists($index, $counts)) {
                        $counts[$index]++;
                    } else {
                        $counts[$index] = 1;
                    }
                }
            }
        }

        foreach ($data as $vuln) {
            if ((!in_array($vuln['vuln'], $ignoredInfos)) && ($vuln['risk'] == "INFO")) {
                $counts[$vuln['ip']]--;
            }
        }
        $started = 0;
        foreach ($data as $vuln) {

            $notes = "<td class=\"black\">N/A</td>";

            $options = array(
                "HIGH"     => "red",
                "CRITICAL" => "red",
                "MEDIUM"   => "orange",
                "LOW"      => "green",
                "INFO"     => "blue",
            );

            if ($vuln['severity'] > 4) {
                $status = 'FAIL';
                $statusColour = 'fail';
            } else {
                $status = 'PASS';
                $statusColour = 'pass';
            }

            if ($vuln['severity'] == 0) {
                $vuln['severity'] = 'N/A';
                $sevColour = "black";
            } else {
                $sevColour = $options[$vuln['risk']];
            }

            if ((!in_array($vuln['vuln'], $ignoredInfos)) && ($vuln['risk'] == "INFO")) {
                continue;
            }

            if (array_key_exists($vuln['service'], $tidyNames)) {
                $service = $tidyNames[$vuln['service']];
            } else {
                $service = $vuln['service'];
            }

            if ($vuln['vuln'] == "Nessus SYN scanner") {
                $vuln['vuln'] = "Special Note";
            }

            if (($vuln['vuln'] == "Service Detection") || ($vuln['vuln'] == "Special Note")) {
                $vuln['vuln'] = $vuln['vuln'] . ": " . $service;
            }

            if (($vuln['vuln'] == "Service Detection: File Transfer Protocol") || ($vuln['vuln'] == "Service Detection: Telnet Protocol")) {
                $notes = "<td class=\"orange\">CLEAR TEXT</td>";
                $status = "FAIL";
                $statusColour = "red";
            }

            if ($ip == long2ip($vuln['ip'])) {

                print("
            <tr>
                  <td>" . $vuln['vuln'] . "</td>
                  <td>" . $vuln['port'] . "</td>
                  <td class=" . $options[$vuln['risk']] . ">" . $vuln['risk'] . "</td>
                  <td class=" . $sevColour . ">" . $vuln['severity'] . "</td>
                  <td class=" . $statusColour . ">" . $status . "</td>
                  " . $notes . "
             </tr>
            ");
            } else {
                if ($started == 0) {
                    $started++;
                } else {
                    print('
                <tr>
                  <td class="black">Host Summary:</td>
                  <td class="black" colspan=6>Unable to resolve.</td>
                </tr>
            ');
                }

                print("
            <tr>
                  <td border:solid 1pt gray; vertical-align: top; rowspan=\"" . $counts[$vuln['ip']] . "\">" . long2ip($vuln['ip']) . "</td>
                  <td>" . $vuln['vuln'] . "</td>
                  <td>" . $vuln['port'] . "</td>
                  <td class=" . $options[$vuln['risk']] . ">" . $vuln['risk'] . "</td>
                  <td class=" . $sevColour . ">" . $vuln['severity'] . "</td>
                  <td class=" . $statusColour . ">" . $status . "</td>
                  " . $notes . "
             </tr>

            ");

                $ip = long2ip($vuln['ip']);
            }

        }

        echo "</table>";

    }

    public function vulnerabilities($reportData)
    {
        echo '

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/main.css">
<title>Vulnerability View</title>
<script language=\"javascript\">

function loadingScreen(){
   document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
}
</script>
</head>

<a onclick="loadingScreen()" class="myButton" href="/">Return to Menu</a></p>
<table border=0 cellpadding=0 cellspacing=0>

';


        $data = array();
        $rowCount = 0;
        foreach ($reportData as $hostData) {
            $potentialOperatingSystems = explode(PHP_EOL, $hostData['OS']);
            $OS = trim(array_shift($potentialOperatingSystems));

            if ($hostData->fqdn == "") {
                $name = $hostData['netbios'];
            } else {
                $name = $hostData['fqdn'];
            }

            if (!$name) {
                $name = "Unable to accurately identify";
            }

            foreach ($hostData['vulnerabilities'] as $vulnerability) {
                $data[] = array(
                    'ip'       => ip2long($hostData['hostname']),
                    'name'     => $name,
                    'os'       => $OS,
                    'vuln'     => $vulnerability['name'],
                    'risk'     => $vulnerability['risk'],
                    'severity' => $vulnerability['severity']);
            }

        }

        usort($data, function ($firstArrayElement, $secondArrayElement) {
            $first = $firstArrayElement['ip'];
            $second = $secondArrayElement['ip'];

            $ret = strcmp($first, $second);
            if ($ret == 0) {
                $firstSeverity = (float)$firstArrayElement['severity'];
                $secondSeverity = (float)$secondArrayElement['severity'];

                if ($secondSeverity > $firstSeverity) {
                    return 1;
                } elseif ($firstSeverity > $secondSeverity) {
                    return -1;
                } elseif ($firstSeverity == $secondSeverity) {
                    return 0;
                }
            }

            return $ret;
        });

        $ip = "";


        $counts = array();
        foreach ($data as $value) {
            foreach ($value as $key2 => $value2) {
                if ($key2 == "ip") {
                    $index = $value2;
                    if (array_key_exists($index, $counts)) {
                        $counts[$index]++;
                    } else {
                        $counts[$index] = 1;
                    }
                }
            }
        }


        foreach ($data as $vuln) {
            if (($vuln['risk'] == "High") || ($vuln['risk'] == "Critical")) {
                $colour = "red";
            } elseif ($vuln['risk'] == "Medium") {
                $colour = "orange";
            } elseif ($vuln['risk'] == "Low") {
                $colour = "green";
            } elseif ($vuln['risk'] == "Info") {
                $colour = "blue";
            } else {
                $colour = "blue";
            }


            if ($ip == long2ip($vuln['ip'])) {
                $rowCount++;
                print("
            <tr>
                <td class=" . $colour . ">" . $vuln['vuln'] . "</td>
                <td class=" . $colour . ">" . $vuln['risk'] . "</td>
                <td class=" . $colour . ">" . $vuln['severity'] . "</td>
            </tr>
            ");
            } else {
                $rowCount++;
                if ($rowCount > 900)
                {
                    echo "</table>";
                    echo "<p><hr>";
                    echo "<p><table border=0 cellpadding=0 cellspacing=0>";
                    $rowCount = 0;
                }
                print("
            <tr>
                <td border:solid 1pt gray; vertical-align: top; rowspan='" . $counts[$vuln['ip']] . "'>" . long2ip($vuln['ip']) . "</td>
                <td border:solid 1pt gray; vertical-align: top; rowspan='" . $counts[$vuln['ip']] . "'>" . $vuln['name'] . "</td>
                <td border:solid 1pt gray; vertical-align: top; rowspan='" . $counts[$vuln['ip']] . "'>" . $vuln['os'] . "</td>
                <td class=" . $colour . ">" . $vuln['vuln'] . "</td>
                <td class=" . $colour . ">" . $vuln['risk'] . "</td>
                <td class=" . $colour . ">" . $vuln['severity'] . "</td>
            </tr>

            ");

                $ip = long2ip($vuln['ip']);


            }

        }
    }

    public function descriptions($reportData)
    {

      echo '

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/main.css">
<title>Descriptions View</title>
<script language=\"javascript\">

function loadingScreen(){
   document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
}
</script>
</head>

<a onclick="loadingScreen()" class="myButton" href="/">Return to Menu</a></p>
<table border=0 cellpadding=0 cellspacing=0>

';

            foreach ($reportData as $vulnerability)
            {
                echo "<div class=\"main\">";
                echo "<b>" . $vulnerability[0]['vulnerability'] . "</b><br>";
                if ($vulnerability[0]->randomstormed == 1)
                {
                    echo '<font color="green"><i>This has been updated by a RandomStorm staff member</i><br><br></font>';
                } else {
                    echo '<font color="red"><i>This has NOT been updated by a RandomStorm staff member</i><br><br></font>';
                }
                echo "<b>Synopsis</b><br>";
                echo $vulnerability[0]['synopsis'] . "<br>";
                echo "<b>Description</b><br>";
                echo $vulnerability[0]['description'] . "<br>";
                echo "<b>Solution</b><br>";
                echo $vulnerability[0]['solution'] . "<br>";
                echo "<b>Plugin Family</b><br>";
                echo $vulnerability[0]['pluginFamily'] . "<br>";
                echo "<b>CVE References</b><br>";
                echo $vulnerability[0]['cve'] . "<br>";
                echo "<b>Risk Factor</b><br>";
                echo $vulnerability[0]['risk_factor'] . "<br>";
                echo "<b>See Also</b><br>";
                echo $vulnerability[0]['see_also'];

                echo "<hr>";
                echo "</div>";
            }

    }

    public function openDLP($return)
    {


echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<link rel="stylesheet" type="text/css" href="/css/main.css">';
echo '<title>Report Descriptions</title>';
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
echo "<a onclick='loadingScreen()' class=\"myButton\"; href=\"/opendlp\">Return to Menu</a></p>";


echo '<p>Scan Details';
echo '<p><table>';
foreach ( $return as $mainKey => $mainValue)
{
    if ( $mainKey == 'systems' )
    {
        continue;
    }
    echo "
    <tr>
        <td>" . $mainKey . "</td>
        <td>" . $mainValue . "</td>
    </tr>
    ";
}
echo '</table>';




foreach ($return['systems'][0] as $systems)
{
    echo '<p>System';
    echo '<p><table>';
    foreach ( $systems as $systemKey => $systemValue)
    {
        if (( !$systemValue) || ( $systemKey == 'results') || $systemKey == 'resultType')
        {
            continue;
        }
        echo "
        <tr>
            <td>" . $systemKey . "</td>
            <td>" . $systemValue . "</td>
        </tr>
        ";
    }
    echo '</table>';
    echo '<p>Type Count';
    echo '<p><table>';
    foreach ( $systems['resultType'] as $typesKey => $typesValue)
    {
        echo "
        <tr>
            <td>" . $typesKey . "</td>
            <td>" . $typesValue . "</td>
        </tr>
        ";
    }
    echo '</tr>';
    echo '</table>';
    echo '<p>File Locations and Count';
    echo '<p><table>';
    echo '<tr><td class=\"main\">Location</td><td class=\"main\">Count</td>';
    foreach ( $systems['results'] as $resultsKey => $resultsValue)
    {
        echo "
        <tr>
            <td>" . $resultsKey . "</td>
            <td>" . $resultsValue . "</td>
        </tr>
        ";
    }
    echo '</tr>';
    echo '</table>';

}

echo '</div>';



    }
}