<?php
defined('_JEXEC') or die();
JHTML::_('behavior.keepalive');

include_once JPATH_LIBRARIES.'/fof/include.php' ;

FOFTemplateUtils::addCSS('com_j4schema/css/main.css');
FOFTemplateUtils::addCSS('com_j4schema/css/classes.css');
FOFTemplateUtils::addCSS('com_j4schema/css/tree.css');
FOFTemplateUtils::addCSS('com_j4schema/css/frontend.css');

// Dispatch
FOFDispatcher::getAnInstance('com_j4schema')->dispatch();