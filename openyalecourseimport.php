<?php
	$tagsArr = array('education', 'finance', 'economics', 'yale');
	$category = 'education>finance>financial theory';
	
	$baseUrl = 'http://oyc.yale.edu';
	$baseUrlVideo = 'http://openmedia.yale.edu/projects';
	$firstCoursePage = 'http://oyc.yale.edu/political-science/plsc-270/lecture-6';
	
	require('phpQuery.php');
	echo '<?xml version="1.0"?>
<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ingestion.xsd">
	<channel>';
	
	$markup = file_get_contents($firstCoursePage);
	$doc = phpQuery::newDocumentHTML($markup);
	phpQuery::selectDocument($doc);
	$title = pq('div#session_body h2')->text();
	$description = pq('div#overview p')->text();
	$hdVideo = pq('a#course_media_high')->attr('href');
	$hdVideo = $baseUrlVideo.substr($hdVideo, strrpos($hdVideo, '/courses'));
	$rawEmbedJS = pq('div#video_wrapper')->html();
	preg_match('/"captions-2": {.*file: "(.*)",/is', $rawEmbedJS, $matches);
	$cpationsFile = trim($matches[1]);
	?>
		<item>
			<action>add</action>
			<type>1</type>
			<userId>zohar.babin@kaltura.com</userId>
			<name><?php print $title; ?></name>
			<description><?php print $description; ?></description>
			<tags><?php foreach ($tagsArr as $tag) print '<tag>'.trim($tag).'</tag>'; ?></tags>
			<categories><category><?php print $category; ?></category></categories>
			<media>
				<mediaType>1</mediaType> 
			</media>
			<contentAssets>
				<content>
					<urlContentResource url="<?php print $hdVideo ?>"></urlContentResource>
				</content>
			</contentAssets>
			<subTitles>
				<subTitle isDefault="true" format="1" lang="English">
					<tags>
						<tag>English</tag>
					</tags>
					<urlContentResource url="<?php echo $cpationsFile; ?>"></urlContentResource>
				</subTitle>
			</subTitles>
		</item>
	<?
	$nextCoursePage = trim(pq('ul.course_links li#next a')->attr('href'));
	while ($nextCoursePage != '') :
		$markup = file_get_contents($baseUrl . $nextCoursePage);
		$doc = phpQuery::newDocumentHTML($markup);
		phpQuery::selectDocument($doc);
		$title = pq('div#session_body h2')->text();
		$description = pq('div#overview p')->text();
		$hdVideo = pq('a#course_media_high')->attr('href');
		$hdVideo = $baseUrlVideo.substr($hdVideo, strrpos($hdVideo, '/courses'));
		$rawEmbedJS = pq('div#video_wrapper')->html();
		preg_match('/"captions-2": {.*file: "(.*)",/is', $rawEmbedJS, $matches);
		$cpationsFile = trim($matches[1]);
		$nextCoursePage = trim(pq('ul.course_links li#next a')->attr('href'));
	?>
		<item>
			<action>add</action>
			<type>1</type>
			<userId>zohar.babin@kaltura.com</userId>
			<name><?php print $title; ?></name>
			<description><?php print $description; ?></description>
			<tags><?php foreach ($tagsArr as $tag) print '<tag>'.trim($tag).'</tag>'; ?></tags>
			<categories><category><?php print $category; ?></category></categories>
			<media>
				<mediaType>1</mediaType> 
			</media>
			<contentAssets>
				<content>
					<urlContentResource url="<?php print $hdVideo ?>"></urlContentResource>
				</content>
			</contentAssets>
			<subTitles>
				<subTitle isDefault="true" format="1" lang="English">
					<tags>
						<tag>English</tag>
					</tags>
					<urlContentResource url="<?php echo $cpationsFile; ?>"></urlContentResource>
				</subTitle>
			</subTitles>
		</item>
	<?php endwhile; ?>
	</channel>
</mrss>