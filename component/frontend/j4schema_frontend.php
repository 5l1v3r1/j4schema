<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */
defined('_JEXEC') or die();
JHTML::_('behavior.keepalive');

include_once JPATH_ROOT.'/libraries/f0f/include.php' ;

F0FTemplateUtils::addCSS('com_j4schema/css/main.css');
F0FTemplateUtils::addCSS('com_j4schema/css/classes.css');
F0FTemplateUtils::addCSS('com_j4schema/css/tree.css');
F0FTemplateUtils::addCSS('com_j4schema/css/frontend.css');

// Dispatch
F0FDispatcher::getAnInstance('com_j4schema')->dispatch();