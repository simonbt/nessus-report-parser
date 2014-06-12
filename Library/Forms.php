<?php
/**
 * slim -- Forms.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 19:46
 */

namespace Library;


class Forms extends Files
{

    public function deleteNessus($files)
    {
        $nessusDirectory = __DIR__ . '/Uploads/Nessus/';

        if (count($files) > 1)
        {
            return 'multiple';
        }
        elseif(!$files)
        {
            return 'none';
        }
        else
        {
            unlink($nessusDirectory . $this->decodeName($files[0]));
            return 'success';
        }
    }

    public function deleteOpenDLP($files)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/';

        if (count($files) > 1)
        {
            return 'multiple';
        }
        elseif (!$files)
        {
            return 'none';
        }
        else
        {
            unlink($openDlpDirectory . $this->decodeName($files[0]));
            return 'success';
        }

    }

    public function merge($mergeResults)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/';

        if (!$mergeResults)
        {
            return 'none';
        }
        else
        {
            $toMerge = '';
            foreach ($mergeResults as $report)
            {
                $toMerge = $toMerge . ' ' . $nessusDirectory . $this->decodeName($report);
            }
            $command = 'python ' . __DIR__ . '/merger.py ' . $this->decodeName($mergeResults[0]) . $this->rand_string(4) . '.merged ' . $toMerge . ' 2>&1';
            exec($command, $output, $return);
            if ($return == 0)
            {
                return 'success';
            }
            else
            {
                return 'failed';
            }
        }
    }

    public function import($postReport)
    {

        if (count($postReport) > 1)
        {
            return 'multiple';
        }
        elseif (count($postReport) == 0 )
        {
            return 'none';
        }
        else
        {
            return $this->decodeName($postReport[0]);
        }

    }

    public function uploadNessus($tempName, $fileName)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/';

        if (move_uploaded_file($tempName, $nessusDirectory . $fileName))
        {
            return 'success';
        }
        else
        {
            return 'failed';
        }
    }

    public function uploadOpenDLP($tempName, $fileName)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/';

        if (move_uploaded_file($tempName, $openDlpDirectory . $fileName))
        {
            return 'success';
        }
        else
        {
            return 'failed';
        }
    }

} 