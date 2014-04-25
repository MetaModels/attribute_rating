<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeRating
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Fields
 */
	$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['rating_disabled'] = array('Disable rating (implicit on in the backend)', 'If this is checked, the rating will be disabled and only the current rating value will get shown (this is hardcoded to "on" for the Backend).');