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

        if (count($files) > 1) {
            echo 'You can only delete one at a time!';
            print '<p><a href="/files">Return to menu</a>';
        } else {
            foreach ($files as $file) {
                echo "Deleting " . $this->decodeName($file);
                unlink($nessusDirectory . $this->decodeName($file));
                print '<p>Report deleted successfully';
                print '<p><a href="/files">Return to menu</a>';
            }
        }
    }

    public function deleteOpenDLP($files)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/';

        if (count($files) > 1) {
            echo 'You can only delete one at a time!';
            print '<p><a href="/files">Return to menu</a>';
        } else {
            foreach ($files as $file) {
                echo "Deleting " . $this->decodeName($file);
                unlink($openDlpDirectory . $this->decodeName($file));
                print '<p>Report deleted successfully';
                print '<p><a href="/files">Return to menu</a>';
            }
        }
    }

    public function merge($mergeResults)
    {

        $nessusDirectory = __DIR__ . '/uploads/nessus/';


        $toMerge = '';
        foreach ($mergeResults as $report) {
            $toMerge = $toMerge . ' ' . $nessusDirectory . $this->decodeName($report);
        }
        $command = 'python ' . __DIR__ . '/merger.py ' . $this->decodeName($mergeResults[0]) . $this->rand_string(4) . '.merged ' . $toMerge . ' 2>&1';
        exec($command, $output, $return);
        if ($return == 0) {
            print 'Reports merged successfully';
            print '<p><a href="/files">Return to menu</a>';
        } else {
            print_r($output);
        }

    }

    public function import($postReport)
    {

        if (count($postReport) > 1) {
            echo "You can only import one report at a time";
            print '<p><a href="/files">Return to menu</a>';
        } else {
            foreach ($postReport as $report) {
                $reportName = $this->decodeName($report);
            }
        }
        return $reportName;
    }

    public function uploadNessus($tempName, $fileName)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/';

        if (move_uploaded_file($tempName,
            $nessusDirectory . $fileName)
        ) {
            print '<p> The file has been successfully uploaded </p>';
            print '<a href="/files">Return to menu</a>';
        } else {
            print 'The File has failed to upload';
            print '<p><a href="/files">Return to menu</a></p>';

        }
    }

    public function uploadOpenDLP($tempName, $fileName)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/';

        if (move_uploaded_file($tempName,
            $openDlpDirectory . $fileName)
        ) {
            print '<p> The file has been successfully uploaded </p>';
            print '<a href="/files">Return to menu</a>';
        } else {
            print 'The File has failed to upload';
            print '<p><a href="/files">Return to menu</a></p>';

        }
    }

} 