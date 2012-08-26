<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerOverrides extends FOFController
{
	public function copyOverrides()
	{
		jimport('joomla.filesystem.folder');
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html.php';

		$keys   = JFolder::folders(JPATH_COMPONENT_ADMINISTRATOR.'/overrides', '.', false, false);
		$values = JFolder::folders(JPATH_COMPONENT_ADMINISTRATOR.'/overrides', '.', false, true);

		$j4s = array_combine($keys, $values);

		$tmpl_path = JPATH_ROOT.'/templates/'.J4schemaHelperHtml::getFrontendTemplate().'/html/';

		//let's copy the custom overrides
		foreach($j4s as $folder => $path)
		{
			//uh oh, override folder alredy exists, let's backup it
			if(JFolder::exists($tmpl_path.$folder))
			{
				//backup folder already exists, delete it (keep only the last override)
				if(JFolder::exists($tmpl_path.$folder.'_bck'))
				{
					if(!JFolder::delete($tmpl_path.$folder.'_bck'))
					{
						$msg = JText::_('COM_J4SCHEMA_ERR_BCK_DELETE_OVERRIDES');
						break;
					}
				}

				//copy current override folder as backup
				if(!JFolder::copy($tmpl_path.$folder, $tmpl_path.$folder.'_bck'))
				{
					$msg = JText::_('COM_J4SCHEMA_ERR_BCK_COPY_OVERRIDE');
					break;
				}

				if(!JFolder::delete($tmpl_path.$folder))
				{
					$msg = JText::_('COM_J4SCHEMA_ERR_DELETE_FOLDER');
					break;
				}
			}

			if(!JFolder::copy($path, $tmpl_path.$folder))
			{
				$msg = JText::_('COM_J4SCHEMA_ERR_COPY_OVERRIDE');
				break;
			}
		}

		if(!$msg) $msg  = JText::_('COM_J4SCHEMA_OVERRIDE_COPY_OK');
		else	  $type = 'error';

		$this->setRedirect('index.php?option=com_j4schema&view=overrides', $msg, $type);
	}
}