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

namespace MetaModels\Test\Attribute\Rating;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Attribute\Rating\AttributeTypeFactory;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Test the attribute factory.
 */
class RatingAttributeTypeFactoryTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $tableName        The table name.
     *
     * @param string $language         The language.
     *
     * @param string $fallbackLanguage The fallback language.
     *
     * @return IMetaModel
     */
    protected function mockMetaModel($tableName, $language, $fallbackLanguage)
    {
        $metaModel = $this->getMockBuilder('MetaModels\IMetaModel')->getMock();

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue($language));

        $metaModel
            ->expects($this->any())
            ->method('getFallbackLanguage')
            ->will($this->returnValue($fallbackLanguage));

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
     * Override the method to run the tests on the attribute factories to be tested.
     *
     * @return IAttributeTypeFactory[]
     */
    protected function getAttributeFactories()
    {
        $connection   = $this->mockConnection();
        $router       = $this->getMockBuilder(RouterInterface::class)->getMock();
        $session      = $this->getMockBuilder(SessionInterface::class)->getMock();
        $scopeMatcher = $this->mockScopeMatcher();

        return array(new AttributeTypeFactory($connection, $router, $session, $scopeMatcher));
    }

    /**
     * Test creation of a numeric attribute.
     *
     * @return void
     */
    public function testCreateRating()
    {
        $connection   = $this->mockConnection();
        $router       = $this->getMockBuilder(RouterInterface::class)->getMock();
        $session      = $this->getMockBuilder(SessionInterface::class)->getMock();
        $scopeMatcher = $this->mockScopeMatcher();

        $factory   = new AttributeTypeFactory($connection, $router, $session, $scopeMatcher);
        $values    = array(
            'rating_max'   => 10,
            'rating_half'  => 1,
            'rating_emtpy' => '',
            'rating_full'  => '',
            'rating_hover' => '',
        );
        $attribute = $factory->createInstance(
            $values,
            $this->mockMetaModel('mm_test', 'de', 'en')
        );

        $this->assertInstanceOf('MetaModels\Attribute\Rating\Rating', $attribute);

        foreach ($values as $key => $value) {
            $this->assertEquals($value, $attribute->get($key), $key);
        }
    }
}
