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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Rating;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\AbstractAttributeTypeFactory;
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
    private $connection;

    /**
     * Router.
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * Session.
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * Request scope determinator.
     *
     * @var RequestScopeDeterminator
     */
    private $scopeDeterminator;

    /**
     * Construct.
     *
     * @param Connection               $connection        Database connection.
     * @param RouterInterface          $router            Router.
     * @param SessionInterface         $session           Session
     * @param RequestScopeDeterminator $scopeDeterminator Scope determinator.
     */
    public function __construct(
        Connection $connection,
        RouterInterface $router,
        SessionInterface $session,
        RequestScopeDeterminator $scopeDeterminator
    ) {
        $this->typeName          = 'rating';
        $this->typeIcon          = 'bundles/metamodelsattributerating/star-full.png';
        $this->typeClass         = 'MetaModels\Attribute\Rating\Rating';
        $this->connection        = $connection;
        $this->router            = $router;
        $this->session           = $session;
        $this->scopeDeterminator = $scopeDeterminator;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new $this->typeClass(
            $metaModel,
            $information,
            $this->connection,
            $this->router,
            $this->session,
            $this->scopeDeterminator
        );
    }
}
