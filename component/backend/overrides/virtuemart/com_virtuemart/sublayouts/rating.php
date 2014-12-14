<?php defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];

$addMicro = isset($viewData['rating_reviews']) ? true : false;

if ($viewData['showRating']) {
	$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
	if (empty($product->rating)) {
	?>
		<div class="ratingbox dummy" title="<?php echo vmText::_('COM_VIRTUEMART_UNRATED'); ?>" >

		</div>
	<?php
	} else {
		$ratingwidth = $product->rating * 24;
  ?>
<div <?php echo $addMicro ? '{VM_RATING_WRAPPER}' : ''?>>
	<?php echo $addMicro ? '{VM_META_RATING_REVIEWS_COUNT:'.$viewData['rating_reviews'].'}' : ''?>
	<div title=" <?php echo (vmText::_("COM_VIRTUEMART_RATING_TITLE") . round($product->rating) . '/' . $maxrating) ?>" class="ratingbox" >
	  <div class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>"></div>
	</div>
	<?php echo JText::_('COM_VIRTUEMART_RATING') . ' <span '.($addMicro ? '{VM_RATING}' : '').'>' . round(3, 2) . '</span>/<span '.($addMicro ? '{VM_MAX_RATING}' : '').'>' . $maxrating.'</span>'; ?>
</div>
	<?php
	}
}