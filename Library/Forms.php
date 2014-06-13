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

    public function deleteNessus($files, $userId)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/' . $userId .'/';

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

    public function deleteOpenDLP($files, $userId)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/' . $userId . '/';

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

    public function merge($mergeResults, $userId)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/' . $userId . '/';

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
            $command = 'python ' . __DIR__ . '/merger.py ' . $this->decodeName($mergeResults[0]) . $this->rand_string(4) . '.merged ' . $_SESSION['userId'] . ' ' . $toMerge . ' 2>&1';
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

    public function uploadNessus($tempName, $fileName, $userId)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/' . $userId . '/';

        if (move_uploaded_file($tempName, $nessusDirectory . $fileName))
        {
            return 'success';
        }
        else
        {
            return 'failed';
        }
    }

    public function uploadOpenDLP($tempName, $fileName, $userId)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/' . $userId . '/';

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