<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemJ4sjintegration extends JPlugin
{
	public function onAfterRender()
	{
		function timeToISO($value)
		{
			$datetime = str_replace('{ARTICLE_PUBLISH_UP:', '', $value[0]);
			$datetime = str_replace('}', '', $datetime);
			$date = new JDate($datetime);
			$iso  = $date->toISO8601();
			return 'itemprop="datePublished" datetime="'.$iso.'"';
		}

		$body = JResponse::getBody();

		// -- SINGLE ARTICLE TOKEN REPLACE --
		$body = str_replace('{ARTICLE_WRAPPER}', 'itemscope itemtype="http://schema.org/WebPage"', $body);
		$body = str_replace('{ARTICLE_BODY}', 'itemprop="mainContentOfPage"', $body);
		$body = str_replace('{ARTICLE_TITLE}', 'itemprop="name"', $body);
		$body = str_replace('{ARTICLE_LINK}', 'itemprop="url"', $body);
		$body = str_replace('{ARTICLE_CATEGORY}', 'itemprop="genre"', $body);
		$body = str_replace('{ARTICLE_LINKS}', 'itemprop="significantLinks"', $body);
		$body = preg_replace_callback('#\{ARTICLE_PUBLISH_UP:.*\}#', 'timeToISO', $body);

		// -- BLOG LAYOUT TOKEN REPLACE --
		//$body = str_replace();

		JResponse::setBody($body);
	}
}
