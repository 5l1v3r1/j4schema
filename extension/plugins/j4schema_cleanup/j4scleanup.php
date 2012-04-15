<?php
/**
 * @package J4Schema
 * @copyright Copyright (c)2011 Davide Tampellini
 * @license GNU General Public License version 3, or later
 * @since 1.0
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgContentJ4scleanup extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$replace = 'itemscope';
		$article->text = str_replace('itemscope=""', $replace, $article->text);
	}
}
