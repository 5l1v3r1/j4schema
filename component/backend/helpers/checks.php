<?php
/**
 * @package J4Schema
 * @copyright Copyright (c)2011-2014 Davide Tampellini
 * @license GNU General Public License version 3, or later
 * @since 1.0
 */
defined('_JEXEC') or die('Restricted access');

class J4schemaHelperChecks
{
	public static function fullCheck()
	{
		$warning  = array();

		$jce 	   = self::checkJCE();

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

	public static function checkMootools()
	{
		jimport('joomla.plugin.helper');
		$check = JPluginHelper::isEnabled('system', 'mtupgrade');

		if(!$check)
		{
			$warning = '<div style="margin-bottom:5px">Mootools System upgrade not enabled.<br/>
							You <strong>MUST</strong> enable it in order to use J4Schema</div>';
		}

		return $warning;
	}

	public static function checkJCE()
	{
		jimport('joomla.plugin.helper');
		$jce = JPluginHelper::isEnabled('editors', 'jce');
		$warning = '';

		if(!$jce)
		{
			$warning = '<div style="margin-bottom:5px">JCE editor is not installed.<br />
						 You <strong>MUST</strong> enable it in order to use J4Schema</div>';

			return $warning;
		}

        // Removed HTML cleanup check, since it's creating a lot of false positives

		//$params = JComponentHelper::getParams('com_jce');
		//$cleanHTML = $params->get('editor.verify_html');

        /*$db = JFactory::getDbo();

        // In new JCE versions the verify HTML flag is set inside another extension
        $query = $db->getQuery(true)
                    ->select($db->qn('params'))
                    ->from('#__extensions')
                    ->where($db->qn('name').' = '.$db->q('plg_editors_jce'));
        $raw = $db->setQuery($query)->loadResult();

        $paramsAlt = new JRegistry();

        if($raw)
        {
            $paramsAlt->loadString($raw);
        }

        $cleanHTMLAlt = $paramsAlt->get('verify_html');

		if($cleanHTMLAlt)
		{
			$warning .= '<div style="margin-bottom:5px">JCE is cleaning up your html.<br />
						 You <strong>MUST</strong> disable it, otherwise JCE will strip out microdata information</div>';
		}*/

		return $warning;
	}
}