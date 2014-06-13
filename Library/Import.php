<?php
/**
 * slim -- Import.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 20:47
 */

namespace Library;


class Import extends ReportsAbstract
{

    protected $xmlObj;
    protected $reportName;
    protected $reportID;

    public function importNessusXML($userId, $fileName)
    {
        $nessusDirectory = __DIR__ . '/uploads/nessus/' . $userId . '/';
        $xmlFile = $nessusDirectory . $fileName;

        $this->createReport($userId, $xmlFile); // Output any return from report import.

        return 'success';
    }

    public function createReport($userId, $xml) // Create report in database and spawn further functions for vulnerabilities and hosts.
    {
        date_default_timezone_set('Europe/London');
        $this->xmlObj = simplexml_load_file($xml);
        $this->reportName = $this->xmlObj->Report[0]['name'];
        $createReport = $this->getPdo()->prepare('INSERT INTO reports (report_name, created, userid) VALUES(?, ?, ?)');
        $createdOk = $createReport->execute(array($this->xmlObj->Report[0]['name'], date('Y-m-d H:i:s'), $userId));
        if (!$createdOk) {
            die('Sorry, we couldn\'t create the new report: ' . print_r($createReport->errorInfo()) . PHP_EOL);
        }

        $this->reportID = $this->getPdo()->lastInsertId();
        $this->createHost();

        return $this->reportName;
    }

    public function completeReport() // Complete report - NOT YET IMPLEMENTED
    {
        $completedInsert = $this->getPdo()->prepare('UPDATE reports SET imported = 1 WHERE report_id = ?');
        $reportCompleted = $completedInsert->execute(array('1'));
        if (!$reportCompleted) {
            die('Sorry, we couldn\'t complete the report insert: ' . print_r($completedInsert->errorInfo()) . PHP_EOL);
        }

        return $this->getPdo()->lastInsertId();
    }

    private function createHost() // Create host ready to have vulnerabilities assigned. This will always create a new host for each report.
    {
        echo "<p>";
        $count = 1;
        $insertHost = $this->getPdo()->prepare('INSERT INTO hosts (report_id, host_name) VALUES(?, ?)');
        foreach ($this->xmlObj->Report[0]->ReportHost as $num => $host) {
            echo 'Importing host ' . $count . ' of ' . $this->xmlObj->Report[0]->ReportHost->count() . '... ';
            $insertedHost = $insertHost->execute(array($this->reportID, $host['name']));
            if (!$insertedHost) {
                die('Sorry, we couldn\'t insert the host: ' . print_r($insertHost->errorInfo()) . PHP_EOL);
            }

            $hostID = $this->getPdo()->lastInsertId();
            $properties = $host[0]->HostProperties->children();
            /* @var SimpleXMLElement $properties */

            $this->addHostDetails($hostID, $properties);

            $this->addVulnerability($host, $hostID);

            $count++;
        }
    }

    private function addHostDetails($hostID, $properties) // Add all host details such as FQDN, Operating system etc to the database
    {
        foreach ($properties as $tagItem) /* @var SimpleXMLElement $tagItem */ {

            $names = array('mac-address', 'system-type', 'operating-system', 'host-ip', 'host-fqdn', 'netbios-name');

            $attribs = $tagItem->attributes();
            $name = $attribs['name'];
            $value = (string)$tagItem;
            $hostUpdate = $this->getPdo()->prepare('UPDATE hosts SET ' . str_replace('-', '_', $name) . '=? WHERE id=?');

            if (in_array($name, $names)) {
                $updateHost = $hostUpdate->execute(array($value, $hostID));
                if (!$updateHost) {
                    die('Sorry, we couldn\'t update the host: ' . print_r($hostUpdate->errorInfo()) . PHP_EOL);
                }
            }

        }
    }

    private function addVulnerability($host, $hostID) // Add vulnerabilities. This will add the vulnerability if it doesn't yet exist,
    { // and will add a link between the host and that vulnerability including the protocol and port recorded.
        $foundVulnerabilities = array();

        foreach ($host->ReportItem as $item) /* @var SimpleXMLElement $item */ {
            $attributes = array();
            if (!$item->cvss_base_score) {
                $cvss = 0.0;
            } else {
                $cvss = $item->cvss_base_score;
            }


            $addVuln = $this->getPdo()->prepare('INSERT IGNORE INTO vulnerabilities (pluginID, vulnerability, svc_name, severity, pluginFamily, description, cve, risk_factor, see_also, solution, synopsis) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $addVulnLink = $this->getPdo()->prepare('INSERT INTO host_vuln_link (report_id, host_id, plugin_id, port, protocol, service) VALUES(?, ?, ?, ?, ?, ?)');

            foreach ($item->attributes() as $attribute => $value) {

                if ($attribute != 'pluginName') {
                    $attributes[$attribute] = (string)$value;
                }

            }
            $vulnAdded = $addVuln->execute(array($attributes['pluginID'], $item['pluginName'], $attributes['svc_name'], $cvss, $attributes['pluginFamily'], $item->description, $item->cve, $item->risk_factor, $item->see_also, $item->solution, $item->synopsis));
            if (!$vulnAdded) {
                die('Sorry, we couldn\'t add the vulnerability: ' . print_r($addVuln->errorInfo()) . PHP_EOL);
            }


            $vulnLinkAdded = $addVulnLink->execute(array($this->reportID, $hostID, $attributes['pluginID'], $attributes['port'], $attributes['protocol'], $attributes['svc_name']));
            if (!$vulnLinkAdded) {
                die('Sorry, we couldn\'t add the vulnerability link: ' . print_r($addVulnLink->errorInfo()) . PHP_EOL);
            }

            $foundVulnerabilities[$attributes['pluginID']] = (string)$item['pluginName'];

        }

        echo "Found " . count($foundVulnerabilities) . " vulnerabilities<br>";

    }

} 