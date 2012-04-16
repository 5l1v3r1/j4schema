<?php
/**
 * @package J4Schema
 * @copyright Copyright (c)2011 Davide Tampellini
 * @license GNU General Public License version 3, or later
 * @since 1.0
 */
defined('_JEXEC') or die('Restricted access');

class J4schemaHelperChecks
{
	public static function fullCheck()
	{
		$warning = array();

		$j4splugin = self::checkJ4SPlugin();
		$jce 	   = self::checkJCE();

		if($j4splugin) 	$warning[] = $j4splugin;
		if($jce)		$warning[] = $jce;

		return $warning;
	}

	public static function checkJ4SPlugin()
	{
		jimport('joomla.plugin.helper');
		$j4s = JPluginHelper::isEnabled('content', 'j4scleanup');

		if(!$j4s)
		{
			$warning = '<div style="margin-bottom:5px">J4Schema Cleanup plugin is not enabled.<br />
						 You <strong>MUST</strong> enable it, otherwise microdata attribute will not display correctly </div>';
		}

		return $warning;
	}

	public static function checkJCE()
	{
		jimport('joomla.plugin.helper');
		$jce = JPluginHelper::isEnabled('editors', 'jce');

		if(!$jce)
		{
			$warning = '<div style="margin-bottom:5px">JCE editor is not installed.<br />
						 You <strong>MUST</strong> enabled it in order to use J4Schema</div>';

			return $warning;
		}

		$params = JComponentHelper::getParams('com_jce');
		$cleanHTML = $params->get('editor.verify_html');

		if($cleanHTML)
		{
			$warning .= '<div style="margin-bottom:5px">JCE is cleaning up your html.<br />
						 You <strong>MUST</strong> disable it, otherwise JCE will strip out microdata information</div>';
		}

		return $warning;
	}
}