<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_rating
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

/**
 * Table tl_metamodel_attribute
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['rating extends _complexattribute_'] = [
    '+display' => [
        'rating_max after description',
        'rating_half',
        'rating_emtpy',
        'rating_full',
        'rating_hover',
    ],
    '+advanced' => [
        '-isvariant',
    ],
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_max'] = [
    'label'                  => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_max'],
    'exclude'                => true,
    'inputType'              => 'text',
    'eval'                   => [
        'includeBlankOption' => true,
        'doNotSaveEmpty'     => true,
        'tl_class'           => 'w50',
        'rgxp'               => 'digit',
    ],
    'sql'                    => 'int(10) NOT NULL default \'0\''
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_half'] = [
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_half'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'eval'                  => [
        'tl_class'          => 'w50 m12 cbx',
    ],
    'sql'                    => 'char(1) NOT NULL default \'\''
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_emtpy'] = [
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_emtpy'],
    'exclude'               => true,
    'inputType'             => 'fileTree',
    'eval'                  => [
        'fieldType'         => 'radio',
        'filesOnly'         => true,
        'files'             => true,
        'extensions'        => 'jpg,png,gif',
        'tl_class'          => 'clr',
    ],
    'sql'                    => 'blob NULL'
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_full'] = [
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_full'],
    'exclude'               => true,
    'inputType'             => 'fileTree',
    'eval'                  => [
        'fieldType'         => 'radio',
        'filesOnly'         => true,
        'files'             => true,
        'extensions'        => 'jpg,png,gif',
        'tl_class'          => 'clr',
    ],
    'sql'                    => 'blob NULL'
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['rating_hover'] = [
    'label'                 => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['rating_hover'],
    'exclude'               => true,
    'inputType'             => 'fileTree',
    'eval'                  => [
        'fieldType'         => 'radio',
        'filesOnly'         => true,
        'files'             => true,
        'extensions'        => 'jpg,png,gif',
        'tl_class'          => 'clr',
    ],
    'sql'                    => 'blob NULL'
];
