<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2021 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_rating
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2021 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Test\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use MetaModels\AttributeRatingBundle\ContaoManager\Plugin;
use MetaModels\CoreBundle\MetaModelsCoreBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Unit tests the contao manager plugin.
 *
 * @covers \MetaModels\AttributeRatingBundle\ContaoManager\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * Test that plugin can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $plugin = new Plugin();

        $this->assertInstanceOf(Plugin::class, $plugin);
        $this->assertInstanceOf(BundlePluginInterface::class, $plugin);
    }

    /**
     * Tests that the a valid bundle config is created.
     *
     * @return void
     */
    public function testBundleConfig()
    {
        $parser  = $this->getMockBuilder(ParserInterface::class)->getMock();
        $plugin  = new Plugin();
        $bundles = $plugin->getBundles($parser);

        $this->assertContainsOnlyInstancesOf(BundleConfig::class, $bundles);
        $this->assertCount(1, $bundles);

        /** @var BundleConfig $bundleConfig */
        $bundleConfig = $bundles[0];

        $this->assertEquals($bundleConfig->getLoadAfter(), [ContaoCoreBundle::class, MetaModelsCoreBundle::class]);
        $this->assertEquals($bundleConfig->getReplace(), ['metamodelsattribute_rating']);
    }

    /**
     * Test if the routing config is loaded.
     *
     * @return void
     */
    public function testRouting()
    {
        $loader   = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $resolver = $this->getMockBuilder(LoaderResolverInterface::class)->getMock();

        $loader
            ->expects($this->once())
            ->method('load');

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($loader);

        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();

        $plugin = new Plugin();
        $plugin->getRouteCollection($resolver, $kernel);
    }
}
