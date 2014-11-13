<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package     MetaModels
 * @subpackage  AttributeRating
 * @author      Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author      Christian de la Haye <service@delahaye.de>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

/**
 * Table tl_metamodel_attribute
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['rating extends _complexattribute_'] = array(
    '+display' => array(
        'rating_max after description',
        'rating_half',
        'rating_emtpy',
        'rating_full',
        'rating_hover',
    ),
    '+advanced' => array(
        '-isvariant',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_max'] = array(
    'label'                  => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_max'],
    'exclude'                => true,
    'inputType'              => 'text',
    'eval'                   => array(
        'includeBlankOption' => true,
        'doNotSaveEmpty'     => true,
        'tl_class'           => 'w50',
        'rgxp'               => 'digit',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_half'] = array(
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_half'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'eval'                  => array(
        'tl_class'          => 'w50 m12 cbx',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_emtpy'] = array(
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_emtpy'],
    'exclude'               => true,
    'inputType'             => 'fileTree',
    'eval'                  => array(
        'fieldType'         => 'radio',
        'filesOnly'         => true,
        'files'             => true,
        'extensions'        => 'jpg,png,gif',
        'tl_class'          => 'clr',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_full'] = array(
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_full'],
    'exclude'               => true,
    'inputType'             => 'fileTree',
    'eval'                  => array(
        'fieldType'         => 'radio',
        'filesOnly'         => true,
        'files'             => true,
        'extensions'        => 'jpg,png,gif',
        'tl_class'          => 'clr',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_hover'] = array(
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_hover'],
    'exclude'               => true,
    'inputType'             => 'fileTree',
    'eval'                  => array(
        'fieldType'         => 'radio',
        'filesOnly'         => true,
        'files'             => true,
        'extensions'        => 'jpg,png,gif',
        'tl_class'          => 'clr',
    ),
);
