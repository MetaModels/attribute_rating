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
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Test\DependencyInjection;

use MetaModels\AttributeRatingBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeRatingBundle\Controller\RateAjaxController;
use MetaModels\AttributeRatingBundle\DependencyInjection\MetaModelsAttributeRatingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $container = new ContainerBuilder();

        $extension = new MetaModelsAttributeRatingExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasDefinition('metamodels.attribute_rating.factory'));
        $definition = $container->getDefinition('metamodels.attribute_rating.factory');
        self::assertCount(1, $definition->getTag('metamodels.attribute_factory'));
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testRatingControllerIsRegistered()
    {
        $container = new ContainerBuilder();

        $extension = new MetaModelsAttributeRatingExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasDefinition('metamodels.controller.rating'));
        $definition = $container->getDefinition('metamodels.controller.rating');
        self::assertEquals(RateAjaxController::class, $definition->getClass());
    }
}
