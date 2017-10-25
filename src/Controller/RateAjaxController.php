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
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Rating\Controller;

use MetaModels\Attribute\Rating\Rating;
use MetaModels\IFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the MetaModelAttribute ajax endpoint for the rating attribute.
 *
 * @package    MetaModels
 * @subpackage AttributeRatingAjax
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 *
 * @codeCoverageIgnore
 */
class RateAjaxController
{
    /**
     * Metamodels factory.
     *
     * @var IFactory
     */
    private $factory;

    /**
     * RatingAjaxController constructor.
     *
     * @param IFactory $factory Metamodels factory.
     */
    public function __construct(IFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Set HTTP 400 Bad Request header and exit the script.
     *
     * @param string $message The error message.
     *
     * @return void
     *
     * @throws BadRequestHttpException With the given message.
     */
    protected function bail($message = 'Invalid AJAX call.')
    {
        throw new BadRequestHttpException($message);
    }

    /**
     * Process an ajax request.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function ratingAction(Request $request)
    {
        $arrData  = $request->request->get('data');
        $fltValue = $request->request->get('rating');

        if (!($arrData && $arrData['id'] && $arrData['pid'] && $arrData['item'])) {
            $this->bail('Invalid request.');
        }

        $objMetaModel = $this->factory->getMetaModel($this->factory->translateIdToMetaModelName($arrData['pid']));
        if (!$objMetaModel) {
            $this->bail('No MetaModel.');
        }

        /** @var Rating $objAttribute */
        $objAttribute = $objMetaModel->getAttributeById($arrData['id']);

        if (!$objAttribute) {
            $this->bail('No Attribute.');
        }

        $objAttribute->addVote($arrData['item'], floatval($fltValue), true);

        return new Response();
    }
}
