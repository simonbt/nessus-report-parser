<?php
/**
 * slim -- Common.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 18:50
 */

namespace Library;


class Common extends files{

    public function nessusIndex($reports, $severity)
    {
        echo '  <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link rel="stylesheet" type="text/css" href="css/main.css">
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


                    function loadingScreen(){
                        document.body.innerHTML += "<div class="loading">Loading&#8230;</div>";
                    }
                </script>
            </head>
<div class="menu">
<div><a href="/" onclick="loadingScreen()"><img src="images/logo.png" alt="RandomStorm Limited" /></a></div>
<div>
<br>
<b>Options</b>
<br>
<p><a class="myButton"; href="/opendlp" onclick="loadingScreen()">OpenDLP Reports</a>
<p><a class="myButton"; href="/files" onclick="loadingScreen()">File Management</a>
<p><a class="myButton"; href="/logout" onclick="loadingScreen()">Logout</a>

<p><b>Imported Nessus Reports</b>
';

        if (!$reports)
        {
            echo "There are no reports available on the system<br>";
        } else {
            foreach ($reports as $report) {
                echo '<p><b>' . $report['report_name'] . '</b> (' . $report['created'] . ') - ';
                echo '
<label>
    <select onchange="location = this.options[this.selectedIndex].value; loadingScreen()">
        <option selected> Select Report </option>
        <option value="hosts/' . $report['id'] . '/' . $severity . '">Hosts Report</option>
        <option value="vulnerabilities/' . $report['id'] . '/' . $severity . '">Vulnerabilities Report</option>
        <option value="descriptions/' . $report['id'] . '/' . $severity . '">Descriptions Report</option>
        <option value="pci/' . $report['id'] . '">PCI Report</option>
    </select>
</label>

            ';
            }
            echo '</div>';
        }
    }

    public function openDlpIndex()
    {



echo '  <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link rel="stylesheet" type="text/css" href="/css/main.css">
                <title>RandomStorm Report Generator</title>
                <script language="javascript">

';
echo "
                    function loadingScreen(){
                        document.body.innerHTML += '<div class=\"loading\">Loading&#8230;</div>';
                    }
                </script>
            </head>
";

echo '<div class="menu">';
echo '<div><a href="/opendlp" onclick="loadingScreen()"><img src="/images/logo.png" alt="RandomStorm Limited" /></a></div>';
echo '<div>
<br>
<b>Options</b>
<br>
<p><a class="myButton"; href="/" onclick="loadingScreen()">Nessus Reports</a>
<p><a class="myButton"; href="/files" onclick="loadingScreen()">File Management</a>
<p><a class="myButton"; href="/logout" onclick="loadingScreen()">Logout</a>

<p><b>Imported OpenDLP Reports</b>
';



if (!$this->getOpenDlpList())
{
    echo "There are no reports available on the system<br>";
} else {

    foreach ($this->getOpenDlpList() as $name => $hash) {
        echo '<p><b>' . $name . '</b> ';
        echo '
<label>
    <select onchange="location = this.options[this.selectedIndex].value; loadingScreen()">
        <option selected> Select Report </option>
        <option value="/opendlp/' . $name . '">OpenDLP Overview Report</option>
    </select>
</label>

            ';
    }
    echo '</div>';
}



    }
} 