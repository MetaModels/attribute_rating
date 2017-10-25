<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeRating
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0
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
