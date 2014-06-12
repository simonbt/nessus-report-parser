import xml.etree.ElementTree as etree
import shutil
import os
import sys

first = 1
for fileName in sys.argv[3:]:
   if ".nessus" in fileName:

      if first:
         mainTree = etree.parse(fileName)
         report = mainTree.find('Report')
         report.attrib['name'] = 'Merged Report'
         first = 0
      else:
         tree = etree.parse(fileName)
         for host in tree.findall('.//ReportHost'):
            existing_host = report.find(".//ReportHost[@name='"+host.attrib['name']+"']")
            if existing_host is not None:

                report.append(host)
            else:
                for item in host.findall('ReportItem'):
                    if not existing_host.find("ReportItem[@port='"+ item.attrib['port'] +"'][@pluginID='"+ item.attrib['pluginID'] +"']"):

                        existing_host.append(item)

mainTree.write(os.path.dirname(os.path.abspath(__file__)) + '/Uploads/Nessus/' + sys.argv[2] + '/' + sys.argv[1], encoding="utf-8", xml_declaration=True)