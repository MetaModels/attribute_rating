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
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Helper;

use Contao\Input;
use MetaModels\Attribute\Rating\Rating;
use MetaModels\IMetaModelsServiceContainer;
use MetaModels\IServiceContainerAware;

/**
 * This is the MetaModelAttribute ajax endpoint for the rating attribute.
 */
class RatingAjax implements IServiceContainerAware
{
    /**
     * The service container.
     *
     * @var IMetaModelsServiceContainer
     */
    protected $serviceContainer;

    /**
     * Set HTTP 400 Bad Request header and exit the script.
     *
     * @param string $message The error message.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function bail($message = 'Invalid AJAX call.')
    {
        \header('HTTP/1.1 400 Bad Request');

        die('Rating Ajax: '.$message);
    }

    /**
     * {@inheritdoc}
     */
    public function setServiceContainer(IMetaModelsServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function getServiceContainer()
    {
        // Implicit fallback if none set, retrieve from global container.
        if (!$this->serviceContainer) {
            $this->serviceContainer = $GLOBALS['container']['metamodels-service-container'];
        }

        return $this->serviceContainer;
    }

    /**
     * Process an ajax request.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function handle()
    {
        $input = Input::getInstance();
        if (!$input->get('metamodelsattribute_rating')) {
            return;
        }

        $arrData  = $input->post('data');
        $fltValue = $input->post('rating');

        if (!($arrData && $arrData['id'] && $arrData['pid'] && $arrData['item'])) {
            $this->bail('Invalid request.');
        }
        $factory      = $this->getServiceContainer()->getFactory();
        $objMetaModel = $factory->getMetaModel($factory->translateIdToMetaModelName($arrData['pid']));
        if (!$objMetaModel) {
            $this->bail('No MetaModel.');
        }

        /** @var Rating $objAttribute */
        $objAttribute = $objMetaModel->getAttributeById($arrData['id']);

        if (!$objAttribute) {
            $this->bail('No Attribute.');
        }

        $objAttribute->addVote($arrData['item'], (float) $fltValue, true);

        \header('HTTP/1.1 200 Ok');
        exit;
    }
}
