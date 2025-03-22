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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Attribute;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\AbstractAttributeTypeFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Attribute type factory for rating attributes.
 */
class AttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * Router.
     *
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * Request scope determinator.
     *
     * @var RequestScopeDeterminator
     */
    private RequestScopeDeterminator $scopeDeterminator;

    /**
     * The application path.
     *
     * @var string
     */
    private string $appRoot;

    /**
     * The public web folder.
     *
     * @var string
     */
    private string $webDir;

    /**
     * The Request stack.
     *
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * Construct.
     *
     * @param Connection               $connection        Database connection.
     * @param RouterInterface          $router            Router.
     * @param RequestScopeDeterminator $scopeDeterminator Scope determinator.
     * @param string                   $appRoot           The application path.
     * @param string                   $webDir            The public web folder.
     * @param RequestStack             $requestStack      The Request stack.
     */
    public function __construct(
        Connection $connection,
        RouterInterface $router,
        RequestScopeDeterminator $scopeDeterminator,
        string $appRoot,
        string $webDir,
        RequestStack $requestStack
    ) {
        parent::__construct();
        $this->typeName          = 'rating';
        $this->typeIcon          = 'bundles/metamodelsattributerating/star-full.png';
        $this->typeClass         = Rating::class;
        $this->connection        = $connection;
        $this->router            = $router;
        $this->scopeDeterminator = $scopeDeterminator;
        $this->appRoot           = $appRoot;
        $this->webDir            = $webDir;
        $this->requestStack      = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new Rating(
            $metaModel,
            $information,
            $this->connection,
            $this->router,
            null,
            $this->scopeDeterminator,
            $this->appRoot,
            $this->webDir,
            $this->requestStack
        );
    }
}
