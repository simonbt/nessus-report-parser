<?php
/**
 * slim -- Common.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 18:50
 */

namespace Library;


class Common extends files{

    public function index()
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
<html>

<div class="menu" ><a href="/" onclick="loadingScreen()"><img src="images/logo.png" alt="RandomStorm Limited" /></a>
        <nav>
	<ul>
		<li><a href="#">Home</a></li>
		<li>
      <a href="#">Reports <span class="caret"></span></a>
			<div>
				<ul>
					<li><a href="/nessus">Nessus</a></li>
                    <li><a href="/opendlp">OpenDLP</a></li>
				</ul>
			</div>
		</li>
		<li><a href="/files">File Management</a></li>
		<li><a href="#">' . $_SESSION['name'] . '<span class="caret"></span></a>
		    <div>
				<ul>
                    <li><a href="/admin/adduser">Add User</a></li>
					<li><a href="/admin/changepass">Change Password</a></li>
                    <li><a href="/logout">Logout</a></li>
				</ul>
			</div>
		</li>
	</ul>
</nav>

<p><br><br>Welcome to the RandomStorm Report Generator
<p>Please select reports from above

</div>

</html>
';
    }

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
<html>

<div class="menu" ><a href="/" onclick="loadingScreen()"><img src="images/logo.png" alt="RandomStorm Limited" /></a>
        <nav>
	<ul>
		<li><a href="/">Home</a></li>
		<li>
      <a href="#">Reports <span class="caret"></span></a>
			<div>
				<ul>
					<li><a href="/nessus">Nessus</a></li>
                    <li><a href="/opendlp">OpenDLP</a></li>
				</ul>
			</div>
		</li>
		<li><a href="/files">File Management</a></li>
		<li><a href="#">' . $_SESSION['name'] . '<span class="caret"></span></a>
		    <div>
				<ul>
                    <li><a href="/admin/adduser">Add User</a></li>
					<li><a href="/admin/changepass">Change Password</a></li>
                    <li><a href="/logout">Logout</a></li>
				</ul>
			</div>
		</li>
	</ul>
</nav>





<p><br><br><b>Imported Nessus Reports</b></br>
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

echo '

<div class="menu">
<div class="menu" ><a href="/" onclick="loadingScreen()"><img src="images/logo.png" alt="RandomStorm Limited" /></a></div>
        <nav>
	<ul>
		<li><a href="/">Home</a></li>
		<li>
      <a href="#">Reports <span class="caret"></span></a>
			<div>
				<ul>
					<li><a href="/nessus">Nessus</a></li>
                    <li><a href="/opendlp">OpenDLP</a></li>
				</ul>
			</div>
		</li>
		<li><a href="/files">File Management</a></li>
		<li><a href="#">' . $_SESSION['name'] . '<span class="caret"></span></a>
		    <div>
				<ul>
				    <li><a href="/admin/adduser">Add User</a></li>
					<li><a href="/admin/changepass">Change Password</a></li>
                    <li><a href="/logout">Logout</a></li>
				</ul>
			</div>
		</li>
	</ul>
</nav>


<p><br><br><b>Imported OpenDLP Reports</b>
';



if (!$this->getOpenDlpList())
{
    echo "There are no reports available on the system<br>";
} else {

    foreach ($this->getOpenDlpList() as $name => $hash) {
        echo '<p>' . $name;
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