<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeNumeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Register the templates
 */
TemplateLoader::addFiles(
    array(
        'mm_attr_rating'     => 'system/modules/metamodelsattribute_rating/templates',
        'mm_attr_rating_raw' => 'system/modules/metamodelsattribute_rating/templates',
    )
);
