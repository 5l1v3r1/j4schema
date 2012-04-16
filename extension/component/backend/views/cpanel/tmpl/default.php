<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
	defined( '_JEXEC' ) or die( 'Restricted access' );
	$this->loadHelper('checks');

	$warnings = J4schemaHelperChecks::fullCheck();
	$warn_img = FOFTemplateUtils::parsePath('com_j4schema/images/warning_32.png');
?>

<div class="sx w50_" style="font-family:Helvetica;font-size:14px">
	This extension is managed using J4Schema editor plugin. <br/>
	Here there are no options nor settings to configure (except for component access options).<br/>
	<br/>
	Cheers! :)
</div>

<div class="dx w45_">
<?php if($warnings): ?>
	<div style="font-family:Helvetica;font-size:14px;margin-bottom:15px">
		<img class="sx" style="margin-right:5px" src="<?php echo $warn_img; ?>" />
		Uh oh! There's something wrong<br />
		Check the requirements below
	</div>
<?php echo implode('', $warnings); ?>

<?php endif;?>
</div>

<div class="clr"></div>