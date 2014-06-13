<?php
/**
 * slim -- ReportData.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 16:18
 */

namespace Library;

class ReportData extends ReportsAbstract
{

    function listReports($userId)
    { // List all reports that have been imported into the system

        $reports = array();

        $listReportQuery = $this->getPdo()->prepare('SELECT * FROM reports WHERE userid =? ORDER BY id DESC');
        $listReportQuery->execute(array($userId));
        $reportList = $listReportQuery->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($reportList as $report) {
            array_push($reports, array('id'          => $report['id'],
                                       'report_name' => $report['report_name'],
                                       'created'     => $report['created']));

        }

        return $reports;
    }

    function getDescriptions($reportID, $severity)
    {

        $returnArray = array();
        $getVulnerabilites = $this->getPDO()->prepare('SELECT DISTINCT plugin_id FROM host_vuln_link LEFT JOIN vulnerabilities ON host_vuln_link.plugin_id = vulnerabilities.pluginID WHERE host_vuln_link.report_id=? AND vulnerabilities.severity >=?');
        $getDetails = $this->getPdo()->prepare('SELECT * FROM vulnerabilities WHERE pluginID = ?');
        $getVulnerabilites->execute(array($reportID, $severity));
        $vulnerabilites = $getVulnerabilites->fetchall(\PDO::FETCH_COLUMN);

        foreach ($vulnerabilites as $vulnerability) {
            $getDetails->execute(array($vulnerability));
            $details = $getDetails->fetchAll(\PDO::FETCH_ASSOC);
            $returnArray[$vulnerability] = $details;
        }

        return $returnArray;
    }

    function getVulnerabilities($reportID, $severity)
    { // Returns all data filtered by severity and report ID
        $getHostIDs = $this->getPdo()->prepare('SELECT DISTINCT host_id FROM host_vuln_link WHERE report_id=?');
        $getHostName = $this->getPdo()->prepare('SELECT host_name, operating_system, host_fqdn, netbios_name FROM hosts WHERE id=?');
        $getVulnerabilites = $this->getPDO()->prepare('SELECT DISTINCT plugin_id FROM host_vuln_link LEFT JOIN vulnerabilities ON host_vuln_link.plugin_id = vulnerabilities.pluginID WHERE host_vuln_link.report_id=? AND host_vuln_link.host_id=? AND vulnerabilities.severity >=?');
        $getDetails = $this->getPdo()->prepare('SELECT vulnerability, risk_factor, severity FROM vulnerabilities WHERE pluginID = ?');
        $getHostIDs->execute(array($reportID));
        $hosts = $getHostIDs->fetchall(\PDO::FETCH_ASSOC);
        if (!$hosts) {
            die('Sorry, we couldn\'t get the host ID list: ' . $getHostIDs->errorInfo()[2] . PHP_EOL);
        }

        foreach ($hosts as $key => $host) {
            $getHostName->execute(array($host['host_id']));
            $hostName = $getHostName->fetchall(\PDO::FETCH_ASSOC);
            $hosts[$key]['hostname'] = $hostName[0]['host_name'];
            $hosts[$key]['OS'] = $hostName[0]['operating_system'];
            $hosts[$key]['fqdn'] = $hostName[0]['host_fqdn'];
            $hosts[$key]['netbios'] = $hostName[0]['netbios_name'];
            $getVulnerabilites->execute(array($reportID, $host['host_id'], $severity));
            $vulnerabilites = $getVulnerabilites->fetchall(\PDO::FETCH_COLUMN);

            foreach ($vulnerabilites as $id => $vulnerability) {
                $vulnerabilites[$id] = array();
                $getDetails->execute(array($vulnerability));
                $details = $getDetails->fetchAll(\PDO::FETCH_ASSOC);
                $vulnerabilites[$id]['name'] = $details[0]['vulnerability'];
                $vulnerabilites[$id]['severity'] = $details[0]['severity'];
                $vulnerabilites[$id]['risk'] = $details[0]['risk_factor'];
            }
            $hosts[$key]['vulnerabilities'] = $vulnerabilites;
        }
        return $hosts;
    }

    function getHosts($reportID, $severity)
    { // Returns all report data for all hosts, filtered by severity and report ID but sorted by vulnerability.

        $returnTable = array();
        $getPluginIDs = $this->getPdo()->prepare('SELECT DISTINCT(plugin_id) as id FROM host_vuln_link WHERE report_id = ?');
        $getHostIDs = $this->getPdo()->prepare('SELECT host_id, port, protocol FROM host_vuln_link WHERE plugin_id =? and report_id =?');
        $getHostName = $this->getPdo()->prepare('SELECT host_name FROM hosts WHERE id=?');
        $getDetails = $this->getPdo()->prepare('SELECT * FROM vulnerabilities WHERE pluginID = ? AND severity >=?');
        $getPluginIDs->execute(array($reportID));
        $pluginIDs = $getPluginIDs->fetchAll(\PDO::FETCH_COLUMN);
        if (!$pluginIDs) {
            die('Sorry, we couldn\'t get the plugin ID list: ' . $getPluginIDs->errorInfo()[2] . PHP_EOL);
        }


        foreach ($pluginIDs as $plugin) {

            $getDetails->execute(array($plugin, $severity));
            $details = $getDetails->fetchAll(\PDO::FETCH_ASSOC);
            if (!$details) {
                $index = array_search($plugin, $pluginIDs);
                unset($pluginIDs[$index]);
                continue;
            }

            $getHostIDs->execute(array($plugin, $reportID));
            $hostIDs = $getHostIDs->fetchAll(\PDO::FETCH_ASSOC);
            if (!$hostIDs) {
                die('Sorry, we couldn\'t get the hosts list: ' . $getHostIDs->errorInfo()[2] . PHP_EOL);
            }

            foreach ($hostIDs as $i => $id) {
                $getHostName->execute(array($id['host_id']));
                $hostName = $getHostName->fetch(\PDO::FETCH_COLUMN);

                $hostIDs[$i]['host_id'] = $hostName;
            }

            $returnTable[$plugin] = array($details, $hostIDs);

        }

        return $returnTable;
    }

    function getPCI($reportID)
    { // Returns all report data for all hosts, filtered by severity and report ID but sorted by vulnerability.

        $getHostIDs = $this->getPdo()->prepare('SELECT DISTINCT host_id FROM host_vuln_link WHERE report_id=?');
        $getHostName = $this->getPdo()->prepare('SELECT host_name FROM hosts WHERE id=?');
        $getVulnerabilites = $this->getPDO()->prepare('SELECT DISTINCT plugin_id, protocol, port, service FROM host_vuln_link LEFT JOIN vulnerabilities ON host_vuln_link.plugin_id = vulnerabilities.pluginID WHERE host_vuln_link.report_id=? AND host_vuln_link.host_id=? ORDER BY plugin_id');
        $getDetails = $this->getPdo()->prepare('SELECT vulnerability, risk_factor, severity FROM vulnerabilities WHERE pluginID = ?');
        $getHostIDs->execute(array($reportID));
        $hosts = $getHostIDs->fetchall(\PDO::FETCH_ASSOC);
        if (!$hosts) {
            die('Sorry, we couldn\'t get the host ID list: ' . $getHostIDs->errorInfo()[2] . PHP_EOL);
        }

        foreach ($hosts as $key => $host) {
            $getHostName->execute(array($host['host_id']));
            $hostName = $getHostName->fetchall(\PDO::FETCH_ASSOC);
            $hosts[$key]['hostname'] = $hostName[0]['host_name'];
            $getVulnerabilites->execute(array($reportID, $host['host_id']));
            $vulnerabilites = $getVulnerabilites->fetchall(\PDO::FETCH_ASSOC);

            foreach ($vulnerabilites as $id => $vulnerability) {
                $vulnerabilites[$id] = array();
                $getDetails->execute(array($vulnerability['plugin_id']));
                $details = $getDetails->fetchAll(\PDO::FETCH_ASSOC);
                $vulnerabilites[$id]['plugin'] = $vulnerability['plugin_id'];
                $vulnerabilites[$id]['name'] = $details[0]['vulnerability'];
                $vulnerabilites[$id]['severity'] = $details[0]['severity'];
                $vulnerabilites[$id]['risk'] = $details[0]['risk_factor'];
                $vulnerabilites[$id]['port'] = $vulnerability['port'];
                $vulnerabilites[$id]['protocol'] = $vulnerability['protocol'];
                $vulnerabilites[$id]['service'] = $vulnerability['service'];
            }
            $hosts[$key]['vulnerabilities'] = $vulnerabilites;
        }
        return $hosts;
    }

    protected $xmlObj;
    protected $scanName;
    protected $report;
    protected $reportResults;
    protected $totalFiles;
    protected $totalFilesDone;
    protected $totalBytesFound;
    protected $totalBytesScanned;

    function getOpenDLP($fileName, $userId)
    {
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/' . $userId . '/';
        $xml = $xml = simplexml_load_file($openDlpDirectory . $fileName);

        $this->xmlObj = $xml;
        $this->report = array();

        foreach ($this->xmlObj->systems->system as $target) {
            $typeCount = array();
            foreach ($target->results->result as $result) {
                if (array_key_exists(trim($result->type), $typeCount)) {
                    $typeCount[trim($result->type)]++;
                } else {
                    $typeCount[trim($result->type)] = 1;
                }
            }

            $fileCount = array();
            foreach ($target->results->result as $result) {
                if (array_key_exists(trim($result->file), $fileCount)) {
                    $fileCount[trim($result->file)]++;
                } else {
                    $fileCount[trim($result->file)] = 1;
                }
            }

            $this->totalFiles = $this->totalFiles + $target->filestotal;
            $this->totalFilesDone = $this->totalFilesDone + $target->filesdone;
            $this->totalBytesFound = $this->totalBytesFound + $target->bytestotal;
            $this->totalBytesScanned = $this->totalBytesScanned + $target->bytesdone;

            $systemDetails = array();

            foreach ($target->results->result as $details) {
                $systemDetails[] = array(

                    'Type'            => trim($details->type),
                    // 'Base64'           => trim($details->raw_pattern_base64),
                    'Matched Pattern' => trim($details->filtered_pattern),
                    'File Location'   => trim($details->file),
                    'Offset'          => trim($details->offset),
                    // 'MD5 Hash'           => trim($details->md5),
                    'Database Name'   => trim($details->database),
                    'Table'           => trim($details->table),
                    'Column'          => trim($details->column),
                    'Row'             => trim($details->row)

                );
            }


            $this->reportResults[] = array(

                'System Name'         => trim($target->system_name),
                'Workgroup'           => trim($target->workgroup),
                'IP Address'          => trim($target->ip),
                'Total Files Found'   => trim($target->filestotal),
                'Total Files Scanned' => trim($target->filesdone),
                'Total Bytes Found'   => trim($target->bytestotal),
                'Total Bytes Scanned' => trim($target->bytesdone),
                'Last Updated'        => trim($target->updated),
                'Scan Status'         => trim($target->control),
                'pid'                 => trim($target->pid),
                'Databases Found'     => trim($target->dbtotal),
                'Databases Scanned'   => trim($target->dbdone),
                'Tables Found'        => trim($target->tabletotal),
                'Tables Scanned'      => trim($target->tabledone),
                'Columns Found'       => trim($target->columntotal),
                'Columns Scanned'     => trim($target->columntotal),
                'Scan Type'           => trim($target->scantype),
                'Results Found'       => count($target->results->result),
                'resultType'          => $typeCount,
                'results'             => $fileCount
            );


        }

        $this->report = array(

            'Scan Name'           => trim($this->xmlObj->scanname),
            'Total Files Found'   => $this->totalFiles,
            'Total Files Scanner' => $this->totalFilesDone,
            'Total Bytes Found'   => $this->totalBytesFound,
            'Total Bytes Scanned' => $this->totalBytesScanned,

        );

        $this->report['systems'] = array();
//        foreach( $this->reportResults as $result)
//        {
//            array_push($this->report['systems'], $result);
//        }

        array_push($this->report['systems'], $this->reportResults);

        return $this->report;

    }

} 