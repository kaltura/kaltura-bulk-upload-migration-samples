<?php
require_once(__DIR__.DIRECTORY_SEPARATOR.'Kaltura'.DIRECTORY_SEPARATOR.'KalturaClient.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'KalturaAccountConfiguration.php');
require_once(__DIR__.'/Zend/Feed/Rss.php');

class ImportEntriesFromTedRSSFeed
{
	private $bulkUploadXML = '';
	private $tedTempFileName = 'tempTedBulkUpload.xml';
	
	public static function run()
	{
		if (KalturaAccountConfiguration::PARTNER_ID == 0)
			die("Please fill valid Kaltura credentials in KalturaAccountConfiguration.php".PHP_EOL);
		$bulkImport = new ImportEntriesFromTedRSSFeed();
		$bulkImport->generateBulkUploadXMLFromTedRSS();
		$bulkImport->submitBulkUploadXML();
		echo "Kaltura is now processing this Bulk XML file, .".PHP_EOL;
		echo "check the Bulk Upload Log tab under the Content tab in the Kaltura Management Console".PHP_EOL;
	}
	
	private function generateBulkUploadXMLFromTedRSS() {
		$this->bulkUploadXML = '<?xml version="1.0"?>
<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ingestion.xsd">
	<channel>';
	
		$channel = new Zend_Feed_Rss("http://feeds.feedburner.com/tedtalks_video");
		
		Zend_Feed::registerNamespace("media","http://search.yahoo.com/mrss/");
		Zend_Feed::registerNamespace("itunes","http://www.itunes.com/dtds/podcast-1.0.dtd");
		
		$tags = $channel->{'itunes:keywords'}();
		$tagsArr = explode(',', $tags);
		
		$userId = KalturaAccountConfiguration::USER_EMAIL;
		
		foreach ($channel as $item) {
			$thumb = $item->{'itunes:image'}['url'];
			$videoUrl = $item->{'media:content'}['url'];
			$title = '<![CDATA['.$item->{'itunes:subtitle'}().']]>';
			$description = '<![CDATA['.$item->{'itunes:summary'}().']]>';
			$speaker = $item->{'itunes:author'}();
			$category = $item->category();
			$this->bulkUploadXML .= <<<MARKER
<item>
	<action>add</action>
	<type>1</type>
	<userId>$userId</userId>
	<name>$title</name>
	<description>$description</description>
	<tags>
MARKER;
	
		foreach ($tagsArr as $tag) 
			$this->bulkUploadXML .= '<tag>'.trim($tag).'</tag>';
		
		$this->bulkUploadXML .= <<<MARKER
	</tags>
	<categories>
		<category>$category</category>
	</categories>
	<media>
		<mediaType>1</mediaType> 
	</media>
	<contentAssets>
		<content>
			<urlContentResource url="$videoUrl"></urlContentResource>
		</content>
	</contentAssets>
	<thumbnails>
		<thumbnail isDefault="true">
			<urlContentResource url="$thumb"></urlContentResource>
		</thumbnail>
	</thumbnails>
</item>
MARKER;
		}

		$this->bulkUploadXML .= '</channel>
</mrss>';
		file_put_contents($this->tedTempFileName,$this->bulkUploadXML);
	}
	
	private function submitBulkUploadXML()
	{
		try {
			$client = $this->getKalturaClient();
			// http://www.kaltura.com/api_v3/testmeDoc/index.php?service=media&action=bulkuploadadd
			$bulkUploadData = new KalturaBulkUploadXmlJobData();
			$bulkUploadData->fileName = $this->tedTempFileName;
			$bulkUploadData->emailRecipients = KalturaAccountConfiguration::USER_EMAIL;
			$bulkUploadJob = $client->media->bulkuploadadd($this->tedTempFileName, $bulkUploadData);
			print 'Bulk Upload Job successfully added!'.PHP_EOL;
			print 'Job Id: '.$bulkUploadJob->id.PHP_EOL;
			print 'Uploaded By: '.$bulkUploadJob->uploadedBy.PHP_EOL;
			print 'Log File URL: '.$bulkUploadJob->logFileUrl.PHP_EOL;
			print 'Submitted XML File: '.$bulkUploadJob->bulkFileUrl.PHP_EOL;
		} 
		catch(Exception $ex) {
			die('Kaltura Error: '.$ex->getMessage());
		}
	}
	
	private function getKalturaClient() {
		$kConfig = new KalturaConfiguration(KalturaAccountConfiguration::PARTNER_ID);
		$kConfig->serviceUrl = KalturaAccountConfiguration::SERVICE_URL;
		$client = new KalturaClient($kConfig);
		try
		{
			$loginId = KalturaAccountConfiguration::USER_EMAIL;
			$password = KalturaAccountConfiguration::USER_PASSWORD;
			$partnerId = KalturaAccountConfiguration::PARTNER_ID;
			$result = $client->user->loginbyloginid($loginId, $password, $partnerId);
			$client->setKs($result);
		}
		catch(Exception $ex)
		{
			die("could not start session - check configurations in KalturaAccountConfiguration class");
		}
		
		return $client;
	}

}

ImportEntriesFromTedRSSFeed::run();