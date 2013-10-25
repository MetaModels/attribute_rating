<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Table tl_metamodel_attribute
 */

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['rating extends default'] = array
(
	'+advanced' => array('rating_disabled'),
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['rating_disabled'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['rating_disabled'],
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            => 'w50'
	)
);
