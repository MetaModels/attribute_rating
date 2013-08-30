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
	'MetaModels\Attribute\Rating\Rating' => 'system/modules/metamodelsattribute_rating/MetaModels/Attribute/Rating/Rating.php',
	'MetaModels\Helper\RatingAjax'       => 'system/modules/metamodelsattribute_rating/deprecated/MetaModels/Helper/RatingAjax.php',

	'MetaModelAttributeRatingAjax' => 'system/modules/metamodelsattribute_rating/deprecated/MetaModelAttributeRatingAjax.php',
	'MetaModelAttributeRating'     => 'system/modules/metamodelsattribute_rating/deprecated/MetaModelAttributeRating.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mm_attr_rating'     => 'system/modules/metamodelsattribute_rating/templates',
	'mm_attr_rating_raw' => 'system/modules/metamodelsattribute_rating/templates',
));
