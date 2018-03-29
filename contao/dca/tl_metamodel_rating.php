<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2018 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

/**
 * Table tl_metamodel_rating
 */
$GLOBALS['TL_DCA']['tl_metamodel_rating'] = [
    // Config
    'config' => [
        'sql' => [
            'keys' => [
                'id'             => 'primary',
                'mid,aid,iid'    => 'index'
            ]
        ]
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql'                     => 'int(10) unsigned NOT NULL auto_increment'
        ],
        // model id
        'mid' => [
            'sql'                     => 'int(10) unsigned NOT NULL default \'0\''
        ],
        // attribute id
        'aid' => [
            'sql'                     => 'int(10) unsigned NOT NULL default \'0\''
        ],
        // item id
        'iid' => [
            'sql'                     => 'int(10) unsigned NOT NULL default \'0\''
        ],
        // amount of votes in the DB
        'votecount' => [
            'sql'                     => 'int(10) unsigned NOT NULL default \'0\''
        ],
        // current value
        'meanvalue' => [
            'sql'                     => 'double NULL'
        ]
    ]
];
