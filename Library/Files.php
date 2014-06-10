<?php
/**
 * slim -- Files.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 19:14
 */

namespace Library;

class Files {

    protected $saltPre = 'fd393fncaa7201';
    protected $saltPost = 'asf23180r1nogvbs';

    public function getNessusList()
    {
        $nessusDirectory = __DIR__ . '/Uploads/Nessus';
        $nessusList = array_slice(scandir($nessusDirectory),2);

        $nessusFiles = array();
        foreach ( $nessusList as $fileName )
        {
            $nessusFiles[$fileName] = $this->encodeName($fileName);
        }
        return $nessusFiles;
    }

    public function getOpenDlpList()
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp';
        $openDlpList = array_slice(scandir($openDlpDirectory),2);

        $openDlpFiles = array();
        foreach ( $openDlpList as $fileName )
        {
            $openDlpFiles[$fileName] = $this->encodeName($fileName);
        }

        return $openDlpFiles;
    }

    public function encodeName($fileName)
    {
        $hash = hash('sha512', $this->saltPost . $fileName . $this->saltPost);
        $string = base64_encode(json_encode([$fileName, $hash]));
        return $string;
    }

    public function decodeName($fileHash)
    {
        $data = json_decode(base64_decode($fileHash), true);
        $newHash = hash('sha512', $this->saltPost . $data[0] . $this->saltPost);

        if($data[1] ==  $newHash)
        {
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    public function rand_string($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }

        return $str;
    }


    public function fileIndex() {


echo '  <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link rel="stylesheet" type="text/css" href="/css/main.css">
                <title>RandomStorm Report Generator</title>

                <script language="javascript">
                    function loadingScreen(){
                        document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
                    }
                </script>
            </head>
        <body>
        <div class="menu">
        <div><a href="/" onclick="loadingScreen()"><img src="/images/logo.png" alt="RandomStorm Limited" /></a></div>
        <div><p><a class="myButton" href="/" onclick="loadingScreen()">Return to Nessus reports</a></div>
        <div><p><a class="myButton" href="/opendlp" onclick="loadingScreen()">Return to OpenDLP reports</a></div>
        ';

//Nessus Table
print('
<form action="/files/admin" method="post">
    <h2>Stored Nessus Reports</h2>
');

print '<table class="center">
        <tr>
            <td>Report Name</td>
            <td>Select</td>
        </td>

';
foreach ($this->getNessusList() as $file => $hash)
{
    print'<tr>';
    print('
            <td> ' . $file . ' </td>
            <td><input type="checkbox" name="reports[]" value="' . $hash . '" /></td>
    ');
    print'</tr>';
}

print '</table>';

echo '
        <input type="submit" name="formSubmit" value="Delete Nessus"/>
        <input type="submit" name="formSubmit" value="Merge"/>
        <input type="submit" name="formSubmit" value="Import"/>
    </form>
</body>
</html>
';


print('
<h2>Upload Nessus File</h2>
<form action="/files/upload" method="post" enctype="multipart/form-data">
  <input type="file" name="uploadFile" size="50" maxlength="25" />
  <input type="submit" name="upload" value="Upload Nessus XML"" />
</form>
');


//OpenDLP Table
print('
<form action="/files/admin" method="post">
    <h2>Stored OpenDLP Reports</h2>
');

print '<table class="center">
        <tr>
            <td>Report Name</td>
            <td>Select</td>
        </td>

';
foreach ($this->getOpenDlpList() as $file => $hash)
{
    print'<tr>';
    print('
            <td> ' . $file . ' </td>
            <td><input type="checkbox" name="reports[]" value="' . $hash . '" /></td>
    ');
    print'</tr>';
}

print '</table>';

echo '
        <input type="submit" name="formSubmit" value="Delete OpenDLP"/>
    </form>
</body>
</html>
';

print('
<h2>Upload OpenDLP File</h2>
<form action="/files/upload" method="post" enctype="multipart/form-data">
  <input type="file" name="uploadFile" size="50" maxlength="25" />
  <input type="submit" name="upload" value="Upload OpenDLP XML" />
</form>
</div>
');

    }
} 