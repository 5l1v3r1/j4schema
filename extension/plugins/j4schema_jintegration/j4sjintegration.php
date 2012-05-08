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
			switch ($token->to_type)
			{
				case 'date':
					$this->token = $token;
					$body = preg_replace_callback('#\{'.$token->to_name.':.*\}#', array($this, 'buildDate'), $body);
					$this->token = '';
				break;

				case 'google+':
					$this->token = $token;
					$body = preg_replace_callback('#\{'.$token->to_name.':.*\}#', array($this, 'buildGoogle'), $body);
					$this->token = '';
				break;

				case 'link':
					$body = str_replace('{'.$token->to_name.'}', '<link '.$token->to_replace.' />', $body);
				break;

				case 'meta':
					$this->token = $token;
					$body = preg_replace_callback('#\{'.$token->to_name.':.*\}#', array($this, 'buildMeta'), $body);
					$this->token = '';
				break;

				case 'text':
					$body = str_replace('{'.$token->to_name.'}', $token->to_replace, $body);
				break;
			}
		}

		JResponse::setBody($body);
	}

	function buildDate($value)
	{
		$datetime = str_replace('{'.$this->token->to_name.':', '', $value[0]);
		$datetime = str_replace('}', '', $datetime);

		$iso = $this->timeToISO($datetime);

		return $this->token->to_replace.' datetime="'.$iso.'"';
	}

	function buildGoogle($value)
	{
		$db = JFactory::getDbo();

		$userid = preg_replace('#[^\d]#', '', $value[0]);

		$query = $db->getQuery(true)
					->select('at_profile')
					->from('#__j4schema_authors')
					->where('at_userid = '.$userid);
		$profile = $db->setQuery($query)->loadResult();

		if(!$profile) 	return '';
		else			return 'https://plus.google.com/'.$profile.'?rel=author';
	}

	function buildMeta($value)
	{
		$content = str_replace('{'.$this->token->to_name.':', '', $value[0]);
		$content = str_replace('}', '', $content);

		if(preg_match('#^(\d{4})\-(\d{2})\-(\d{2})#', $content))	$content = $this->timeToISO($content);

		return '<meta '.$this->token->to_replace.' content="'.$content.'" >';
	}

	protected function timeToISO($datetime)
	{
		$date = new JDate($datetime);
		return $date->toISO8601();
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
