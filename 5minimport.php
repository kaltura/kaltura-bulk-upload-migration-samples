<?php
	echo '<?xml version="1.0"?>
<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ingestion.xsd">
	<channel>';
	require_once 'Zend/Feed/Rss.php';
	$baseUrl = 'http://api.5min.com/video/list/info.xml?video_group_id=133115&show_renditions=true';
	$pagesToFetch = 6;
	
	Zend_Feed::registerNamespace('media','http://search.yahoo.com/mrss/');
	for ($i = 1; $i <= $pagesToFetch; ++$i) :
		$fetchUrl = $baseUrl.'&page='.$i;
		$channel = new Zend_Feed_Rss($fetchUrl);
		foreach ($channel as $item) : 
			$thumb = $item->{'media:thumbnail'}['url'];
			$tags = $item->{'media:keywords'}();
			$tagsArr = explode(',', $tags);
			$videoUrls = $item->{'media:content'}();
			$maxbitrate = -1;
			if (is_array($videoUrls)) {
				foreach ($videoUrls as $videoUrlObj) {
					$maxbitrateTemp = intval($videoUrlObj->getAttribute('bitrate'));
					if ($maxbitrateTemp > $maxbitrate) {
						$videoUrl = $videoUrlObj->getAttribute('url');
					}
				}
			} else {
				$videoUrl = $item->{'media:content'}['url'];
			}
			$title = '<![CDATA['.$item->{'media:title'}().']]>';
			$description = $item->{'media:description'}();
			$description = '<![CDATA['.preg_replace('/(.*)<p>(.*)<\/p>/i', '$2', $description).']]>';
			$author = $item->studioName();
			$category = $item->{'media:category'}();
			$category = '<![CDATA['.str_replace('/', '>', $category).']]>';
		?>
			<item>
				<action>add</action>
				<type>1</type>
				<userId><?php print $author; ?></userId>
				<name><?php print $title; ?></name>
				<description><?php print $description; ?></description>
				<tags><tag>5min</tag><?php foreach ($tagsArr as $tag) print '<tag>'.trim(substr($tag, 0, 20)).'</tag>'; ?></tags>
				<categories><category><?php print $category; ?></category></categories>
				<media>
					<mediaType>1</mediaType> 
				</media>
				<contentAssets>
					<content>
						<urlContentResource url="<?php print $videoUrl ?>"></urlContentResource>
					</content>
				</contentAssets>
				<thumbnails>
					<thumbnail>
						<urlContentResource url="<?php print $thumb ?>"></urlContentResource>
					</thumbnail>
				</thumbnails>
			</item>
		<?php endforeach; ?>
	<?php endfor; ?>
	</channel>
</mrss>