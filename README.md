kaltura-bulk-upload-migration-samples
=====================================

Samples directory of scripts that migrate content from public content repositories to Kaltura using Kaltura's XML Bulk Upload Ingestion

This script includes
====================
* Zend/Feed - The Feed part of the Zend Framework. This is used to parse feeds from public content repositories such as Ted.com's Feedburner videos feed.
* phpQuery.php - Library used to parse HTML pages for simplified crawling of pages. This is used to parse pages of public content repositories that don't provide feeds.
* 5minimport.php - Conversion script that migrates 5min.com feed to a Kaltura Bulk Upload XML format.
* latesttedtalksimport.php - Conversion script that migrates Ted.com feedburner feed to a Kaltura Bulk Upload XML format.
* openyalecourseimport.php - Conversion script that migrates courses from http://oyc.yale.edu/ to a Kaltura Bulk Upload XML format.

Usage Guidelines
================
To run the scripts, simply use a PHP CLI, and enter the following:
php [migrationScript].php > outputBulkUpload.xml
The upload outputBulkUpload.xml to your KMC using the Bulk Upload XML ingestion in the Upload menu.

Samples Configuration
=====================
To edit the number of pages to import from 5min's feed:
* Edit 5minimport, set the $pagesToFetch to the number of pages (API results paging) you want to import from 5min.

To edit the course to import from oyc.yale.edu:
* Go to http://oyc.yale.edu/courses , select a course to import, go into the course page, click "VIEW CLASS SESSIONS »", go into the first lecture page.
* Edit openyalecourseimport and paste the URL of the first lecture in the course you wish to import to the variable: "$firstCoursePage". Save.

Notice
======
This sample code is provided for demonstration purposes only. No action to promote scraping or copying of content is encouraged.
Always ask for the content owner permission and retain proper attribution when using content from other sites.