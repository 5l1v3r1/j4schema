<?php
/**
 * @package Joomla4Schema
 * @copyright Copyright (c)2011 Davide Tampellini
 * @license GNU General Public License version 3, or later
 * @since 1.0
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'require.php');

if($controller = JRequest::getWord('c')) {
	if(in_array($controller, $controllers)) require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.strtolower($controller).'.php');
	else jexit('LFI attack');}
else{
	$controller = 'j4schema';
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controller.'.php');}

$doc =& JFactory::getDocument();
$doc->addStylesheet(JURI::root().'administrator/components/'.COMPONENT_NAME.'/assets/css/main.css');
$doc->addStylesheet(JURI::root().'administrator/components/'.COMPONENT_NAME.'/assets/css/classes.css');

// Create the controller
$classname	= 'J4schemaController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getCmd( 'task' ) );

// Redirect if set by the controller
$controller->redirect();