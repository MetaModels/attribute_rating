-- MetaModel for testing the rating attributes.
INSERT INTO `tl_metamodel` (
	`id`, `sorting`, `tstamp`, `name`, `tableName`, `translated`, `languages`, `varsupport`)
VALUES (
1, 256, 1367274071, 'Movies', 'mm_movies', '1', 'a:2:{s:2:"en";a:1:{s:10:"isfallback";s:1:"1";}s:2:"de";a:1:{s:10:"isfallback";s:0:"";}}', '');

-- Attribute with 10 stars and rating half enabled.
INSERT INTO `tl_metamodel_attribute` (
	`id`,
	`pid`,
	`sorting`,
	`tstamp`,
	`name`,
	`description`,
	`colname`,
	`type`,
	`isvariant`,
	`isunique`,
	`rating_max`,
	`rating_half`,
	`rating_emtpy`,
	`rating_full`,
	`rating_hover`)
VALUES (
	1,
	1,
	2432,
	1367884555,
	'a:2:{s:2:"en";s:6:"Rating";s:2:"de";s:7:"Wertung";}',
 'a:2:{s:2:"en";s:0:"";s:2:"de";s:0:"";}',
 'rating',
 'rating',
 '',
 '',
 10,
 '1',
 '',
 '',
 ''
);

-- Attribute like above but with rating half disabled.
INSERT INTO `tl_metamodel_attribute` (
	`id`,
	`pid`,
	`sorting`,
	`tstamp`,
	`name`,
	`description`,
	`colname`,
	`type`,
	`isvariant`,
	`isunique`,
	`rating_max`,
	`rating_half`,
	`rating_emtpy`,
	`rating_full`,
	`rating_hover`)
	VALUES (
		2,
		1,
		2432,
		1367884555,
		'a:2:{s:2:"en";s:7:"Rating2";s:2:"de";s:8:"Wertung2";}',
		'a:2:{s:2:"en";s:0:"";s:2:"de";s:0:"";}',
		'rating2',
		'rating2',
		'',
		'',
		10,
		'0',
		'',
		'',
		''
	);

-- MetaModel table for testing the rating attributes.
CREATE TABLE `mm_movies` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`pid` int(10) unsigned NOT NULL,
	`sorting` int(10) unsigned NOT NULL DEFAULT '0',
	`tstamp` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `mm_movies` (`id`, `pid`, `sorting`, `tstamp`) VALUES (1, 0, 1, 1367884555);

INSERT INTO `tl_metamodel_rating` (`id`, `mid`, `aid`, `iid`, `votecount`, `meanvalue`) VALUES (1, 1, 1, 1, 1, 1.0);

INSERT INTO `tl_metamodel_rating` (`id`, `mid`, `aid`, `iid`, `votecount`, `meanvalue`) VALUES (2, 1, 2, 1, 1, 1.0);
