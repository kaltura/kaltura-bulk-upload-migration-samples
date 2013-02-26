<?php
	require_once(__DIR__.'/Zend/Feed/Rss.php');
	
	$bulkUploadXML = '<?xml version="1.0"?>
<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ingestion.xsd">
	<channel>';

	$channel = new Zend_Feed_Rss("http://feeds.feedburner.com/tedtalks_video");
	
	Zend_Feed::registerNamespace("media","http://search.yahoo.com/mrss/");
	Zend_Feed::registerNamespace("itunes","http://www.itunes.com/dtds/podcast-1.0.dtd");
	
	$tags = $channel->{'itunes:keywords'}();
	$tagsArr = explode(',', $tags);
	
	foreach ($channel as $item) {
		$thumb = $item->{'itunes:image'}['url'];
		$videoUrl = $item->{'media:content'}['url'];
		$title = '<![CDATA['.$item->{'itunes:subtitle'}().']]>';
		$description = '<![CDATA['.$item->{'itunes:summary'}().']]>';
		$speaker = $item->{'itunes:author'}();
		$category = $item->category();
		$bulkUploadXML .= <<<MARKER
<item>
	<action>add</action>
	<type>1</type>
	<userId>zohar.babin@kaltura.com</userId>
	<name>$title</name>
	<description>$description</description>
	<tags>
MARKER;
	
		foreach ($tagsArr as $tag) 
			$bulkUploadXML .= '<tag>'.trim($tag).'</tag>';
		
		$bulkUploadXML .= <<<MARKER
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

	$bulkUploadXML .= '</channel>
</mrss>';
file_put_contents('testted.xml',$bulkUploadXML);