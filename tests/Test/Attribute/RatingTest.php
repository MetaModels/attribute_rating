<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2022 The MetaModels team.
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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Test\Attribute;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\Connection;
use MetaModels\AttributeRatingBundle\Attribute\Rating;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use MetaModels\IMetaModel;

/**
 * Unit tests to test class Rating.
 *
 * @covers \MetaModels\AttributeRatingBundle\Attribute\Rating
 */
class RatingTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $language         The language.
     * @param string $fallbackLanguage The fallback language.
     *
     * @return \MetaModels\IMetaModel
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockForAbstractClass(IMetaModel::class);

        $metaModel
            ->expects(self::any())
            ->method('getTableName')
            ->willReturn('mm_unittest');

        $metaModel
            ->expects(self::any())
            ->method('getActiveLanguage')
            ->willReturn($language);

        $metaModel
            ->expects(self::any())
            ->method('getFallbackLanguage')
            ->willReturn($fallbackLanguage);

        return $metaModel;
    }


    /**
     * Mock the database connection.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    private function mockConnection()
    {
        return $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Mock request scope determinator.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestScopeDeterminator
     */
    private function mockScopeMatcher()
    {
        return $this->getMockBuilder(RequestScopeDeterminator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $connection   = $this->mockConnection();
        $router       = $this->getMockBuilder(RouterInterface::class)->getMock();
        $session      = $this->getMockBuilder(SessionInterface::class)->getMock();
        $scopeMatcher = $this->mockScopeMatcher();
        $appRoot      = '';
        $webDir       = '';
        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();

        $text = new Rating(
            $this->mockMetaModel('en', 'en'),
            [],
            $connection,
            $router,
            $session,
            $scopeMatcher,
            $appRoot,
            $webDir,
            $requestStack
        );
        $this->assertInstanceOf(Rating::class, $text);
    }
}
