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
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_max'][0]         = 'Maximum';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_max'][1]         =
    'Please enter the maximum value for the rating.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_half'][0]        = 'Enable half steps';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_half'][1]        =
    'If this is checked, the rating will count in .5 steps instead of integer values.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_emtpy'][0]       = 'Image for "empty star"';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_emtpy'][1]       = 'Choose the image to use as empty star.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_full'][0]        = 'Image for "full star"';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_full'][1]        = 'Choose the image to use as full star.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_hover'][0]       = 'Image for "hovered star"';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_hover'][1]       = 'Choose the image to use as hovered star.';
