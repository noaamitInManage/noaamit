

--
-- Table structure for table `tb_metatags`
--

CREATE TABLE `tb_metatags` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` tinyint(4) NOT NULL default '0',
  `inner_id` int(11) NOT NULL default '0',
  `meta_title` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `meta_description` text NOT NULL,
  `meta_urlalias` varchar(255) NOT NULL default '',
  `sm_priority` float(3,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `moduleid` (`module_id`,`inner_id`)
) TYPE=MyISAM AUTO_INCREMENT=1;



--------------------------------------------------------------------------------------------

--
-- Table structure for table `tb_metatag_history`
--

CREATE TABLE `tb_metatag_history` (
  `id` int(11) NOT NULL auto_increment,
  `meta_urlalias` varchar(255) NOT NULL default '',
  `module_id` tinyint(4) NOT NULL default '0',
  `inner_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_sitepages`
--

CREATE TABLE `tb_sitepages` (
  `id` int(11) NOT NULL auto_increment,
  `mdl_id` int(11) NOT NULL default '0',
  `mdl_name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `is_static` tinyint(4) NOT NULL default '0',
  `lastupdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;



