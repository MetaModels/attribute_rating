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
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
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

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['rating extends default'] = array(
    '+advanced' => array(
        'rating_disabled',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['rating_disabled'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['rating_disabled'],
    'inputType'               => 'checkbox',
    'eval'                    => array(
        'tl_class'            => 'w50',
    ),
);
