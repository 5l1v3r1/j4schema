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
		$tokens = $this->getTokens();

		$body = JResponse::getBody();

		foreach($tokens as $token)
		{
			if($token->to_type == 'text')
			{
				$body = str_replace('{'.$token->to_name.'}', $token->to_replace, $body);
			}
			elseif($token->to_type == 'date')
			{
				$this->token = $token;
				$body = preg_replace_callback('#\{'.$token->to_name.':.*\}#', array($this, 'timeToISO'), $body);
				$this->token= '';
			}
		}

		JResponse::setBody($body);
	}

	function timeToISO($value)
	{
		$datetime = str_replace('{'.$this->token->to_name.':', '', $value[0]);
		$datetime = str_replace('}', '', $datetime);
		$date = new JDate($datetime);
		$iso  = $date->toISO8601();
		return $this->token->to_replace.' datetime="'.$iso.'"';
	}

	protected function getTokens()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select('*')
					->from('#__j4schema_tokens')
					->where('enabled = 1');
		$rows = $db->setQuery($query)->loadObjectList();

		return $rows;
	}
}
