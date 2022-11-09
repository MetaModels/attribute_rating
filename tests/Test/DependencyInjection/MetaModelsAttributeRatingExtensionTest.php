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
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Test\DependencyInjection;

use MetaModels\AttributeRatingBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeRatingBundle\Controller\RateAjaxController;
use MetaModels\AttributeRatingBundle\DependencyInjection\MetaModelsAttributeRatingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 *
 * @covers \MetaModels\AttributeRatingBundle\DependencyInjection\MetaModelsAttributeRatingExtension
 */
class MetaModelsAttributeRatingExtensionTest extends TestCase
{
    /**
     * Test that extension can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $extension = new MetaModelsAttributeRatingExtension();

        $this->assertInstanceOf(MetaModelsAttributeRatingExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testFactoryIsRegistered()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();

        $container
            ->expects($this->exactly(2))
            ->method('setDefinition')
            ->withConsecutive(
                [
                    'metamodels.attribute_rating.factory',
                    $this->callback(
                        function ($value) {
                            /** @var Definition $value */
                            $this->assertInstanceOf(Definition::class, $value);
                            $this->assertEquals(AttributeTypeFactory::class, $value->getClass());
                            $this->assertCount(1, $value->getTag('metamodels.attribute_factory'));

                            return true;
                        }
                    )
                ],
                [
                    $this->anything(),
                    $this->anything()
                ]
            );

        $extension = new MetaModelsAttributeRatingExtension();
        $extension->load([], $container);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testRatingControllerIsRegistered()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();

        $container
            ->expects($this->atLeastOnce())
            ->method('setDefinition')
            ->withConsecutive(
                [
                    $this->anything(),
                    $this->anything()
                ],
                [
                    'metamodels.controller.rating',
                    $this->callback(
                        function ($value) {
                            /** @var Definition $value */
                            $this->assertInstanceOf(Definition::class, $value);
                            $this->assertEquals(RateAjaxController::class, $value->getClass());

                            return true;
                        }
                    )
                ]
            );

        $extension = new MetaModelsAttributeRatingExtension();
        $extension->load([], $container);
    }
}
