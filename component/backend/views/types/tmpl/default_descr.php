<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

$return['descr'] = $this->item->ty_comment_plain ? $this->item->ty_comment_plain : JText::_('COM_J4SCHEMA_NODESCR');
$return['schema'] = $this->item->ty_url;

echo json_encode($return);