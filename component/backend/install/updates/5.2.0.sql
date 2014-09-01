-- Replace http with https
UPDATE `#__j4schema_tokens` SET `to_replace` = REPLACE(`to_replace`, 'http://schema', 'https://schema');
UPDATE `#__j4schema_properties` SET `pr_url` = REPLACE(`pr_url`, 'http://schema', 'https://schema');
UPDATE `#__j4schema_types` SET `ty_url` = REPLACE(`ty_url`, 'http://schema', 'https://schema');