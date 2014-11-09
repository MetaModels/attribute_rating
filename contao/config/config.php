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

$GLOBALS['METAMODELS']['attributes']['rating']['class'] = 'MetaModels\Attribute\Rating\Rating';
$GLOBALS['METAMODELS']['attributes']['rating']['image'] =
    'system/modules/metamodelsattribute_rating/html/star-full.png';

$GLOBALS['TL_HOOKS']['simpleAjax'][] = array
(
    'MetaModels\Helper\RatingAjax',
    'handle'
);
