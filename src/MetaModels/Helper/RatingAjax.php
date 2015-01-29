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
 * @author     David Greminger <david.greminger@1up.io>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Helper;

use MetaModels\Attribute\Rating\Rating;
use MetaModels\IMetaModelsServiceContainer;
use MetaModels\IServiceContainerAware;

/**
 * This is the MetaModelAttribute ajax endpoint for the rating attribute.
 *
 * @package    MetaModels
 * @subpackage AttributeRatingAjax
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 *
 * @codeCoverageIgnore
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
        header('HTTP/1.1 400 Bad Request');

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
        if (!\Input::get('metamodelsattribute_rating')) {
            return;
        }
        $arrData  = \Input::post('data');
        $fltValue = \Input::post('rating');

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

        $objAttribute->addVote($arrData['item'], floatval($fltValue), true);

        header('HTTP/1.1 200 Ok');
        exit;
    }
}
