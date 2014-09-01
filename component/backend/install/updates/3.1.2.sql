DROP TABLE IF EXISTS `#__j4schema_tokens`;
CREATE TABLE IF NOT EXISTS `#__j4schema_tokens` (
  `id_tokens` int(11) NOT NULL AUTO_INCREMENT,
  `to_integration` varchar(15) NOT NULL,
  `to_name` varchar(50) NOT NULL,
  `to_type` varchar(4) NOT NULL,
  `to_replace` varchar(255) NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_tokens`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

INSERT INTO `#__j4schema_tokens` (`id_tokens`, `to_integration`, `to_name`, `to_type`, `to_replace`, `enabled`) VALUES
(1, 'joomla', 'ARTICLE_WRAPPER', 'text', 'itemscope itemtype="https://schema.org/WebPage"', 1),
(2, 'joomla', 'ARTICLE_BODY', 'text', ' itemprop="mainContentOfPage"', 1),
(3, 'joomla', 'ARTICLE_TITLE', 'text', ' itemprop="name"', 1),
(4, 'joomla', 'ARTICLE_LINK', 'text', ' itemprop="url"', 1),
(5, 'joomla', 'ARTICLE_CATEGORY', 'text', ' itemprop="genre"', 1),
(6, 'joomla', 'ARTICLE_LINKS', 'text', ' itemprop="significantLinks"', 1),
(7, 'joomla', 'ARTICLE_PUBLISH_UP', 'date', ' itemprop="datePublished"', 1),
(11, 'joomla', 'BLOG_ARTICLE_TITLE_LINK', 'text', ' itemprop="url"', 1),
(9, 'joomla', 'BLOG_ARTICLE_TITLE', 'text', 'itemprop="name"', 1),
(10, 'joomla', 'BLOG_POSTS_WRAPPER', 'text', 'itemscope itemtype="https://schema.org/BlogPosting" itemprop="mainContentOfPage"', 1),
(12, 'joomla', 'BLOG_CATEGORY', 'text', ' itemprop="articleSection"', 1),
(13, 'joomla', 'BLOG_TEXT_WRAPPER', 'text', ' itemprop="articleBody"', 1),
(14, 'joomla', 'BLOG_LINKS', 'text', ' itemprop="significantLinks"', 1),
(15, 'joomla', 'BLOG_WRAPPER', 'text', 'itemscope itemtype="https://schema.org/WebPage"', 1),
(16, 'joomla', 'BLOG_CHILDREN_LINK', 'text', ' itemprop="significantLinks"', 1);