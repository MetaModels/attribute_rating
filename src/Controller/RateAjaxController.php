<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Controller;

use MetaModels\AttributeRatingBundle\Attribute\Rating;
use MetaModels\IFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the MetaModelAttribute ajax endpoint for the rating attribute.
 */
class RateAjaxController
{
    /**
     * Metamodels factory.
     *
     * @var IFactory
     */
    private IFactory $factory;

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
     * @return never
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
        $arrData  = $request->request->all('data');
        $fltValue = $request->request->get('rating');

        if (!($arrData && $arrData['id'] && $arrData['pid'] && $arrData['item'])) {
            $this->bail('Invalid request.');
        }

        $objMetaModel = $this->factory->getMetaModel($this->factory->translateIdToMetaModelName($arrData['pid']));
        if (!$objMetaModel) {
            $this->bail('No MetaModel.');
        }

        $objAttribute = $objMetaModel->getAttributeById((int) $arrData['id']);

        if (!$objAttribute instanceof Rating) {
            $this->bail('No Attribute.');
        }

        $objAttribute->addVote($arrData['item'], (float) $fltValue, true);

        return new Response();
    }
}
