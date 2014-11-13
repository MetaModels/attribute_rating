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

namespace MetaModels\Helper;

use MetaModels\Factory;
use MetaModels\Attribute\Rating\Rating;

/**
 * This is the MetaModelAttribute ajax endpoint for the rating attribute.
 *
 * @package    MetaModels
 * @subpackage AttributeRatingAjax
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 *
 * @codeCoverageIgnore
 */
class RatingAjax
{
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
     * Process an ajax request.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function handle()
    {
        if (!\Input::getInstance()->get('metamodelsattribute_rating')) {
            return;
        }
        $arrData  = \Input::getInstance()->post('data');
        $fltValue = \Input::getInstance()->post('rating');

        if (!($arrData && $arrData['id'] && $arrData['pid'] && $arrData['item'])) {
            $this->bail('Invalid request.');
        }

        $objMetaModel = Factory::byId($arrData['pid']);
        if (!$objMetaModel) {
            $this->bail('No MetaModel.');
        }

        // @codingStandardsIgnoreStart - allow this inline doc comment.
        /**
         * @var \MetaModels\Attribute\Rating\Rating $objAttribute
         */
        // @codingStandardsIgnoreEnd

        $objAttribute = $objMetaModel->getAttributeById($arrData['id']);

        if (!$objAttribute) {
            $this->bail('No Attribute.');
        }

        $objAttribute->addVote($arrData['item'], floatval($fltValue), true);

        header('HTTP/1.1 200 Ok');
        exit;
    }
}
