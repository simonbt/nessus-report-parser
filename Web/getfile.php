
<?php

print'
<html>
<head>
<title>Process Uploaded File</title>
</head>
<body>
';

if ( move_uploaded_file ($_FILES['uploadFile'] ['tmp_name'],
    __DIR__ . "/uploads/{$_FILES['uploadFile'] ['name']}")  )
{  print '<p> The file has been successfully uploaded </p>';
    print '<a href="index.php">Return to menu</a>';
}
else
{
    print 'The File has failed to upload';
    print '<p><a href="index.php">Return to menu</a></p>';

}
print'
</body>
</html>
';