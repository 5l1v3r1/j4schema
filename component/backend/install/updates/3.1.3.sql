ALTER TABLE `#__j4schema_tokens` MODIFY `to_type` varchar(10);

REPLACE INTO `#__j4schema_tokens` (`to_integration`, `to_name`, `to_type`, `to_replace`, `enabled`) VALUES
('virtuemart', 'VM_PRODUCT_WRAPPER', 'text', 'itemscope itemtype="https://schema.org/Product"', 1),
('virtuemart', 'VM_PRODUCT_NAME', 'text', ' itemprop="name"', 1),
('virtuemart', 'VM_PRODUCT_DESCR', 'text', ' itemprop="description"', 1),
('virtuemart', 'VM_PRICE_WRAPPER', 'text', ' itemscope itemtype="https://schema.org/Offer" itemprop="offers"', 1),
('virtuemart', 'VM_PRICE', 'text', ' itemprop="price"', 1),
('virtuemart', 'VM_MAIN_IMAGE', 'text', ' itemprop="image"', 1),
('virtuemart', 'VM_RATING_WRAPPER', 'text', ' itemscope itemtype="https://schema.org/AggregateRating" itemprop="aggregateRating"', 1),
('virtuemart', 'VM_RATING', 'text', ' itemprop="ratingValue"', 1),
('virtuemart', 'VM_MAX_RATING', 'text', ' itemprop="bestRating"', 1),
('virtuemart', 'VM_REVIEW', 'text', ' itemscope itemtype="https://schema.org/Review" itemprop="reviews"', 1),
('virtuemart', 'VM_REVIEW_BODY', 'text', ' itemprop="reviewBody"', 1),
('virtuemart', 'VM_REVIEW_RATING_WRAPPER', 'text', ' itemscope itemtype="https://schema.org/Rating" itemprop="reviewRating"', 1),
('virtuemart', 'VM_REVIEW_AUTHOR', 'text', ' itemprop="author"', 1),
('virtuemart', 'VM_PRODUCT_IN_STOCK', 'link', ' itemprop="availability" href="https://schema.org/InStock"', 1),
('virtuemart', 'VM_PRODUCT_OUT_STOCK', 'link', ' itemprop="availability" href="https://schema.org/OutOfStock"', 1),
('joomla', 'GOOGLE_PLUS_AUTHOR', 'google+', '', 1),
('virtuemart', 'VM_META_REVIEW_RATING', 'meta', ' itemprop="ratingValue" ', 1),
('virtuemart', 'VM_META_REVIEW_BEST_RATING', 'meta', ' itemprop="bestRating"', 1),
('virtuemart', 'VM_META_REVIEW_PUBLISH_DATE', 'meta', ' itemprop="datePublished"', 1),
('virtuemart', 'VM_META_RATING_REVIEWS_COUNT', 'meta', ' itemprop="reviewCount"', 1);

CREATE TABLE IF NOT EXISTS `#__j4schema_authors` (
  `id_authors` int(11) NOT NULL AUTO_INCREMENT,
  `at_userid` int(11) NOT NULL,
  `at_profile` varchar(255) NOT NULL,
  PRIMARY KEY (`id_authors`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;