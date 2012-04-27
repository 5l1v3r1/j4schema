<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaHelperFilesystem
{
	public static function treeFolder($path)
	{
		$folders = JFolder::folders($path, '.', false, true);

		foreach($folders as $folder)
		{
			$children = self::treeFolder($folder);

			//if there are no child folder, get the files
			if(!$children)	$children = JFolder::files($folder, '.*\.php');

			$folder = str_replace('\\', '/', $folder);
			$path   = str_replace('\\', '/', $path);
			$folder = str_replace($path.'/', '', $folder);;
			$return[] = array('folder' => $folder, 'children' => $children);
		}

		return $return;
	}
}