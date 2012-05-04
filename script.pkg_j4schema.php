<?php
defined('_JEXEC') or die();

class Pkg_j4schemaInstallerScript
{
	public function preflight($type, $parent)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
					->select('extension_id')
					->from('#__extensions')
					->where('element = '.$db->q('com_j4schema'));
		$ext_id = $db->setQuery($query)->loadResult();

		//no extension_id, maybe we're installing
		if(!$ext_id) return true;

		$query = $db->getQuery(true)
					->select('COUNT(*)')
					->from('#__schemas schema')
					->where('extension_id = '.$ext_id);
		$count = $db->setQuery($query)->loadResult();

		//schema table is already up to date
		if($count) return true;

		//mhm.. no version :-( let's add a dummy one
		$query = $db->getQuery(true)
					->insert('#__schemas')
					->values($ext_id.','.$db->q('3.0.0'));
		$db->setQuery($query)->query();

		return true;
	}

	public function postflight($type, $parent)
	{
		$this->changeTableNames();

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
			$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
			$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_JCE_NOT_INSTALLED').'</em></p>';
			$html[] = '<p>'.JText::_('COM_J4SCHEMA_INSTALL_JCE').'</p>';
			$html[] = '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';

			$this->output($html, false);
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
			$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
			$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_PLUGIN_NOT_FOUND').'</em></p>';
			$html[] = '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';

			$this->output($html, false);
			return true;
		}

		if(!JArchive::extract($pkg_path.'/'.$files[$i], JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins'))
		{
			$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
			$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_ERROR_EXTRACT').'</em></p>';
			$html[] = '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';
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
			$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_CONFIGURATED').'</h2>';
			$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_JCE_DEFAULT_NOT_FOUND').'</em></p>';
			$html[] = '<p>'.JText::_('COM_J4SCHEMA_TOOLBAR_MANUAL').'</p>';

			$this->output($html, false);
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
				$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_CONFIGURATED').'</h2>';
				$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_JCE_PARAMS_ERROR').'</em></p>';
				$html[] = '<p>'.JText::_('COM_J4SCHEMA_TOOLBAR_MANUAL').'</p>';

				$this->output($html, false);
				return true;
			}
		}

		$html[] = '<h2>'.JText::_('COM_J4SCHEMA_TITLE').'</h2>';
		$html[] = '<p>'.JText::_('COM_J4SCHEMA_SUBTITLE').'</p>';

		$this->output($html, true);
		return true;
	}

	/**
	 * Sadly, I had to change table names, so I have to change them manually if I
	 * update the extension (on install everything is already ok)
	 */
	protected function changeTableNames()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					->select('COUNT(*)')
					->from('#__j4s_properties');

		//table names are already ok
		if(!$db->setQuery($query)->loadResult()) return true;

		$rename_query = 'RENAME TABLE
								`#__j4s_properties` TO `#__j4schema_properties`,
								`#__j4s_prop_values` TO `#__j4schema_prop_values`,
								`#__j4s_types` TO `#__j4schema_types`,
								`#__j4s_type_prop` TO `#__j4schema_type_prop`';

		$db->setQuery($rename_query)->query();
	}

	protected function output($html, $success)
	{
		include_once(realpath(dirname(__FILE__)).'/output.php');
	}
}