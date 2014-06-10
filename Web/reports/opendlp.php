<?php
/**
 * nessus-report-parser -- opendlp.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 12:55
 */

require_once(__DIR__ . "/../config.php");

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<link rel="stylesheet" type="text/css" href="main.css">';
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
echo "<a onclick='loadingScreen()' class=\"myButton\"; href=\"../openDLP.php\">Return to Menu</a></p>";

$fileName = $_GET['filename'];

$return = json_decode(getReportData($fileName, $url, $auth)); //Get all report data from the API. Returns JSON so decoding that too

//var_dump($return);


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




foreach ($return->systems[0] as $systems)
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
    foreach ( $systems->resultType as $typesKey => $typesValue)
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
    foreach ( $systems->results as $resultsKey => $resultsValue)
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








function getReportData($fileName, $url, $auth) // Pass reportID, severity and $url from config file to return full report JSON
{
    $query = '?reportid=null&report=5&filename=' . $fileName;
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