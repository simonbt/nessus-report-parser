<?php
/**
 * slim -- Files.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 19:14
 */

namespace Library;

class Files
{

    protected $saltPre = 'fd393fncaa7201';
    protected $saltPost = 'asf23180r1nogvbs';

    public function getNessusList($userId)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/' . $userId;
        $nessusList = array_slice(scandir($nessusDirectory), 2);

        $nessusFiles = array();
        foreach ($nessusList as $fileName)
        {
            $nessusFiles[$fileName] = $this->encodeName($fileName);
        }
        return $nessusFiles;
    }

    public function getOpenDlpList($userId)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/' . $userId;
        $openDlpList = array_slice(scandir($openDlpDirectory), 2);

        $openDlpFiles = array();
        foreach ($openDlpList as $fileName)
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

        if ($data[1] == $newHash) {
            return $data[0];
        } else {
            return false;
        }
    }

    public function rand_string($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }
} 