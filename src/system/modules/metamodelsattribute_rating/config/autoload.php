<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Metamodelsattribute_rating
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MetaModelAttributeRatingAjax' => 'system/modules/metamodelsattribute_rating/MetaModelAttributeRatingAjax.php',
	'MetaModelAttributeRating'     => 'system/modules/metamodelsattribute_rating/MetaModelAttributeRating.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mm_attr_rating'     => 'system/modules/metamodelsattribute_rating/templates',
	'mm_attr_rating_raw' => 'system/modules/metamodelsattribute_rating/templates',
));
