<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

$return['descr'] = $this->item->ty_comment_plain ? $this->item->ty_comment_plain : 'No description provided';
$return['schema'] = $this->item->ty_url;

echo json_encode($return);