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
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['typeOptions']['rating'] = 'Rating';

$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_max']            = array('Maximum', 'Please enter the maximum value for the rating.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_half']           = array('Enable half steps', 'If this is checked, the rating will count in .5 steps instead of integer values.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_emtpy']          = array('Image for "empty star"', 'Choose the image to use as empty star.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_full']           = array('Image for "full star"', 'Choose the image to use as full star.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_hover']          = array('Image for "hovered star"', 'Choose the image to use as hovered star.');
