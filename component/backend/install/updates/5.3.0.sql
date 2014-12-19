-- Replace http with https
INSERT INTO `#__j4schema_tokens` (`id_tokens`, `to_integration`, `to_name`, `to_type`, `to_replace`, `enabled`)
VALUES
  (NULL, 'virtuemart', 'VM_META_REVIEW_WORST_RATING', 'meta', ' itemprop=\"worstRating\"', 1);
