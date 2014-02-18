-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

--
-- Table `tl_metamodel_attribute`
--

CREATE TABLE `tl_metamodel_attribute` (
  `rating_max` int(10) NOT NULL default '0',
  `rating_half` char(1) NOT NULL default '',
  `rating_emtpy` varchar(255) NOT NULL default '',
  `rating_full` varchar(255) NOT NULL default '',
  `rating_hover` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `tl_metamodel_rendersetting`
--

CREATE TABLE `tl_metamodel_rendersetting` (
  `rating_disabled` varchar(32) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `tl_metamodel_rating` -- here the vote information get's stored.
--

CREATE TABLE `tl_metamodel_rating` (
  `id` int(10) unsigned NOT NULL auto_increment,
-- model id
  `mid` int(10) unsigned NOT NULL default '0',
-- attribute id
  `aid` int(10) unsigned NOT NULL default '0',
-- item id
  `iid` int(10) unsigned NOT NULL default '0',
-- amount of votes in the DB
  `votecount` int(10) unsigned NOT NULL default '0',
-- current value
  `meanvalue` double NULL,
  PRIMARY KEY  (`id`),
  KEY `all_id` (`mid`, `aid`, `iid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
