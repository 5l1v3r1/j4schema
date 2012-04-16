<?php
defined('_JEXEC') or die();

class Pkg_j4schemaInstallerScript
{
	public function postflight($type, $parent)
	{
		jimport('joomla.plugin.helper');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');

		$lang = JFactory::getLanguage();
		$lang->load('com_j4schema', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('com_j4schema', JPATH_ADMINISTRATOR, null, true);

		$jce = JPluginHelper::isEnabled('editors', 'jce');

		//JCE is not installed, let's stop here
		if(!$jce)
		{
			echo '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
			echo '<p><em>'.JText::_('COM_J4SCHEMA_JCE_NOT_INSTALLED').'</em></p>';
			echo '<p>'.JText::_('COM_J4SCHEMA_INSTALL_JCE').'</p>';
			echo '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';
			return true;
		}

		$pkg_path = realpath(dirname(__FILE__).'/packages');
		$files = JFolder::files($pkg_path);

		$log_data[] = $pkg_path;
		$log_data[] = print_r($files, true);

		for($i = 0; $i < count($files); $i++)
		{
			if(stripos($files[$i], 'JCE_') !== false)
			{
				$found = true;
				break;
			}
		}

		//if i didn't find the JCE plugin, let's stop here
		if(!$found)
		{
			echo '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
			echo '<p><em>'.JText::_('COM_J4SCHEMA_PLUGIN_NOT_FOUND').'</em></p>';
			echo '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';
			return true;
		}

		if(!JArchive::extract($pkg_path.'/'.$files[$i], JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins'))
		{
			echo '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
			echo '<p><em>'.JText::_('COM_J4SCHEMA_ERROR_EXTRACT').'</em></p>';
			echo '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';
			return true;
		}

		//automatically add the plugin to the "Default" JCE profile
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					->select('*')
					->from('#__wf_profiles')
					->where("name = 'default'");
		$profile = $db->setQuery($query)->loadObject();

		if(!$profile)
		{
			echo '<h2>'.JText::_('COM_J4SCHEMA_NOT_CONFIGURATED').'</h2>';
			echo '<p><em>'.JText::_('COM_J4SCHEMA_JCE_DEFAULT_NOT_FOUND').'</em></p>';
			echo '<p>'.JText::_('COM_J4SCHEMA_TOOLBAR_MANUAL').'</p>';
			return true;
		}

		//check if J4Schema JCE plugin is already configurated
		if(stripos($profile->rows, 'j4schema') === false && stripos($profile->plugins, 'j4schema') === false)
		{
			$query = $db->getQuery(true)
						->update('#__wf_profiles')
						->set('rows = '.$db->quote($profile->rows.',j4schema'))
						->set('plugins = '.$db->quote($profile->plugins.',j4schema'))
						->where('id = '.$profile->id);

			if(!$db->setQuery($query)->query())
			{
				echo '<h2>'.JText::_('COM_J4SCHEMA_NOT_CONFIGURATED').'</h2>';
				echo '<p><em>'.JText::_('COM_J4SCHEMA_JCE_PARAMS_ERROR').'</em></p>';
				echo '<p>'.JText::_('COM_J4SCHEMA_TOOLBAR_MANUAL').'</p>';
				return true;
			}
		}

		echo '<h2>'.JText::_('COM_J4SCHEMA_TITLE').'</h2>';
		echo '<p>'.JText::_('COM_J4SCHEMA_SUBTITLE').'</p>';
		return true;
	}
}