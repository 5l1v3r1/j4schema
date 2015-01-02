REPLACE INTO `#__j4schema_tokens` (`to_integration`, `to_name`, `to_type`, `to_replace`, `enabled`) VALUES
('jevents', 'JE_EVENT_CONTACT', 'text', 'itemscope itemtype="https://schema.org/ContactPoint"', 1),
('jevents', 'JE_EVENT_LOCATION', 'text', 'itemprop="location" itemscope itemtype="https://schema.org/PostalAddress"', 1),
('jevents', 'JE_EVENT_DURATION', 'meta', ' itemprop="duration"', 1),
('jevents', 'JE_EVENT_ENDTIME', 'meta', ' itemprop="endDate"', 1),
('jevents', 'JE_EVENT_NAME', 'text', 'itemprop="name"', 1),
('jevents', 'JE_EVENT_STARTTIME', 'date', ' itemprop="startDate"', 1),
('jevents', 'JE_EVENT_WRAPPER', 'text', 'itemscope itemtype="https://schema.org/Event"', 1);