<?php

/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2019 The MetaModels team.
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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Test;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\Connection;
use MetaModels\AttributeRatingBundle\Attribute\Rating;
use MetaModels\IMetaModel;
use MetaModels\Render\Template;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Test the rating attribute.
 */
class AttributeRatingTest extends TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SessionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $session;

    /**
     * @var AttributeBagInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $sessionBag;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        \Contao\Environment::set('base', 'https://example.com/');
        $GLOBALS['TL_LANG']['metamodel_rating_label'] = '%s %s';
        define('TL_ROOT', __DIR__ . '/../../src/Resources/public');
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $config = new \Doctrine\DBAL\Configuration();
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection(['url' => 'sqlite:///:memory:'], $config);

        // Create the tables now.
        $this->connection->prepare('
        CREATE TABLE `tl_metamodel_rating` (
  `id` int(10),
-- model id
  `mid` int(10) NOT NULL default \'0\',
-- attribute id
  `aid` int(10) NOT NULL default \'0\',
-- item id
  `iid` int(10) NOT NULL default \'0\',
-- amount of votes in the DB
  `votecount` int(10) NOT NULL default \'0\',
-- current value
  `meanvalue` double NULL,
  PRIMARY KEY  (`id`)
);
        ')->execute();

        $this->session = $this->getMockForAbstractClass(SessionInterface::class);
        $this->session
            ->method('getBag')
            ->willReturn($this->sessionBag = $this->getMockForAbstractClass(AttributeBagInterface::class));

        return true;
    }

    /**
     * Test the retrieval of a vote.
     *
     * @return void
     */
    public function testFetchVote()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $this->assertEquals(
            [
                1 => [
                    'votecount' => 1,
                    'meanvalue' => 1.0,
                ],
            ],
            $rating->getDataFor([1])
        );
    }

    /**
     * Test the retrieval of a known and an unknown vote.
     *
     * @return void
     */
    public function testFetchVoteUnknown()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        // Vote for id 2 is not stored in Db and therefore should be empty in the result.
        $this->assertEquals(
            [
                1 => [
                    'votecount' => 1,
                    'meanvalue' => 1.0,
                ],
                2 => [
                    'votecount' => 0,
                    'meanvalue' => 0,
                ],
            ],
            $rating->getDataFor([1, 2])
        );
    }

    /**
     * Test the casting of a vote.
     *
     * @return void
     */
    public function testCastVoteMax()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(1, 10);

        $this->assertEquals(
            array(
                1 => array(
                    'votecount' => 2,
                    'meanvalue' => 1.0,
                ),
            ),
            $rating->getDataFor(array(1))
        );
    }

    /**
     * Test the casting of a vote.
     *
     * @return void
     */
    public function testCastVoteHalf()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(1, 5);

        $this->assertEquals(
            [
                1 => [
                    'votecount' => 2,
                    'meanvalue' => .75,
                ],
            ],
            $rating->getDataFor([1])
        );
    }

    /**
     * Test the casting of a vote.
     *
     * @return void
     */
    public function testUnsetDataFor()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->unsetDataFor([1]);

        $this->assertEquals(
            [
                1 => [
                    'votecount' => 0,
                    'meanvalue' => 0,
                ],
            ],
            $rating->getDataFor([1])
        );
    }

    /**
     * Test the casting of a vote on a locked item.
     *
     * @return void
     */
    public function testTryCastVoteForLockedItem()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);
        $this->sessionBag->method('get')->with('vote_lock_1_1_1')->willReturn(true);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');


        $rating->addVote(1, 5);

        $this->assertEquals(
            [
                1 => [
                    'votecount' => 1,
                    'meanvalue' => 1.0,
                ],
            ],
            $rating->getDataFor([1])
        );
    }

    /**
     * Test the casting of a vote on a locked item.
     *
     * @return void
     */
    public function testTryCastVoteAndLockItem()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);
        $this->sessionBag->method('get')->with('vote_lock_1_1_1')->willReturnOnConsecutiveCalls(false, true);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(1, 10, true);

        $rating->addVote(1, 0, true);

        $this->assertEquals(
            array(
                1 => array(
                    'votecount' => 2,
                    'meanvalue' => 1.0,
                ),
            ),
            $rating->getDataFor(array(1))
        );
    }

    /**
     * Test the casting of a vote on a locked item.
     *
     * @return void
     */
    public function testCastVoteForNewItem()
    {
        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(2, 10);

        $this->assertEquals(
            [
                2 => [
                    'votecount' => 1,
                    'meanvalue' => 1.0,
                ],
            ],
            $rating->getDataFor([2])
        );
    }

    /**
     * Test the sorting of items.
     *
     * @return void
     */
    public function testSortVotes()
    {
        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(2, 10);
        $rating->addVote(3, 5);

        $this->assertEquals(
            [3, 2],
            $rating->sortIds(
                [2, 3], 'ASC')
        );

        $this->assertEquals(
            [2, 3],
            $rating->sortIds([2, 3], 'DESC')
        );

        // invalid ids will get appended.
        $this->assertEquals(
            [2, 3, 4, 5, 6],
            $rating->sortIds([2, 3, 4, 5, 6], 'DESC')
        );
    }

    /**
     * Test the ensure image method.
     *
     * @return void
     */
    public function testPrepareTemplate()
    {
        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating   = $metamodel->getAttribute('rating');
        $itemData = [
            'id'     => 1,
            'rating' => [
                'votecount' => 1,
                'meanvalue' => 1.0,
            ]
        ];
        $template = new Template();
        $settings = $rating->getDefaultRenderSettings();

        $rating->method('ensureImage')->willReturn('star-empty.png');

        $rating->prepareTemplate($template, $itemData, $settings);

        $this->assertEquals($rating, $template->attribute);
        $this->assertEquals($settings, $template->settings);
        $this->assertEquals($itemData, $template->row);
        $this->assertEquals($itemData['rating'], $template->raw);
        $this->assertEquals(16, $template->imageWidth);
        $this->assertEquals('true', $template->rateHalf);
        $this->assertEquals(10, $template->currentValue);

        $this->assertEquals(
            [0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8, 8.5, 9, 9.5, 10],
            $template->options
        );
    }

    /**
     * Test the ensure image method.
     *
     * @return void
     */
    public function testPrepareTemplate2()
    {
        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating   = $metamodel->getAttribute('rating2');
        $itemData = [
            'id'     => 1,
            'rating2' => [
                'votecount' => 300,
                'meanvalue' => 0.65333268,
            ]
        ];
        $template = new Template();
        $settings = $rating->getDefaultRenderSettings();

        $rating->method('ensureImage')->willReturn('star-empty.png');

        $rating->prepareTemplate($template, $itemData, $settings);

        $this->assertEquals($rating, $template->attribute);
        $this->assertEquals($settings, $template->settings);
        $this->assertEquals($itemData, $template->row);
        $this->assertEquals($itemData['rating2'], $template->raw);
        $this->assertEquals(16, $template->imageWidth);
        $this->assertEquals('false', $template->rateHalf);
        $this->assertEquals(7, $template->currentValue);
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $template->options);
    }

    /**
     * Test the getFilterOptions() method.
     *
     * @return void
     */
    public function testGetfilterOptions()
    {
        $this->markTestIncomplete();

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating   = $metamodel->getAttribute('rating');

        // TODO: fill with code when getFilterOptions() is implemented.
        $this->assertEquals(
            array(),
            $rating->getFilterOptions(array(1), false)
        );

        $this->assertEquals(
            array(),
            $rating->getFilterOptions(array(1), true)
        );

        $this->assertEquals(
            array(),
            $rating->getFilterOptions(null, false)
        );

        $this->assertEquals(
            array(),
            $rating->getFilterOptions(null, true)
        );
    }

    /**
     * Test destruction of the auxiliary data.
     *
     * @return void
     */
    public function testDestroyAUX()
    {
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 1,
            'mid'       => 1,
            'aid'       => 1,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);
        $this->connection->insert('tl_metamodel_rating', [
            'id'        => 2,
            'mid'       => 1,
            'aid'       => 2,
            'iid'       => 1,
            'votecount' => 1,
            'meanvalue' => 1.0,
        ]);

        $metamodel = $this->mockMetaModel();

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->destroyAUX();

        $query1 = $this->connection->query('SELECT * FROM tl_metamodel_rating WHERE mid=1 AND aid=1');

        $this->assertEquals(
            [],
            $query1->fetchAll()
        );

        // Ensure the data from the other attribute is still present.
        $query2 = $this->connection->query('SELECT * FROM tl_metamodel_rating WHERE mid=1 AND aid=2');

        $this->assertEquals(
            array(
                array(
                'id'        => 2,
                'mid'       => 1,
                'aid'       => 2,
                'iid'       => 1,
                'votecount' => 1,
                'meanvalue' => 1.0,
                ),
            ),
            $query2->fetchAll()
        );
    }

    /**
     * Mock a MetaModel.
     *
     * @return \MetaModels\IMetaModel|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockMetaModel()
    {
        $metaModel = $this->getMockBuilder(IMetaModel::class)->getMock();

        $metaModel
            ->method('get')
            ->will($this->returnValueMap([
                ['id',         1],
                ['sorting',    256,],
                ['tstamp',     1367274071,],
                ['name',       'Movies',],
                ['tableName',  'mm_movies',],
                ['translated', '1',],
                ['languages',  'a:2:{s:2:"en";a:1:{s:10:"isfallback";s:1:"1";}s:2:"de";a:1:{s:10:"isfallback";s:0:"";}}',],
                ['varsupport', ''],
            ]));

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('mm_unittest'));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue('de'));

        $metaModel
            ->expects($this->any())
            ->method('getFallbackLanguage')
            ->will($this->returnValue('en'));

        // Attribute with 10 stars and rating half enabled.
        $rating = $this->mockRatingAttribute(
            $metaModel,
            [
                'id'           => 1,
                'pid'          => 1,
                'sorting'      => 2432,
                'tstamp'       => 1367884555,
                'name'         => 'a:2:{s:2:"en";s:6:"Rating";s:2:"de";s:7:"Wertung";}',
                'description'  => 'a:2:{s:2:"en";s:0:"";s:2:"de";s:0:"";}',
                'colname'      => 'rating',
                'type'         => 'rating',
                'isvariant'    => '',
                'isunique'     => '',
                'rating_max'   => 10,
                'rating_half'  => '1',
                'rating_emtpy' => '',
                'rating_full'  => '',
                'rating_hover' => '',
            ]
        );
        $rating2 = $this->mockRatingAttribute(
            $metaModel,
            [
                'id'           => 2,
                'pid'          => 1,
                'sorting'      => 2432,
                'tstamp'       => 1367884555,
                'name'         => 'a:2:{s:2:"en";s:7:"Rating2";s:2:"de";s:8:"Wertung2";}',
                'description'  => 'a:2:{s:2:"en";s:0:"";s:2:"de";s:0:"";}',
                'colname'      => 'rating2',
                'type'         => 'rating2',
                'isvariant'    => '',
                'isunique'     => '',
                'rating_max'   => 10,
                'rating_half'  => '',
                'rating_emtpy' => '',
                'rating_full'  => '',
                'rating_hover' => ''
            ]
        );

        $metaModel->method('getAttribute')->will($this->returnValueMap([
            ['rating', $rating],
            ['rating2', $rating2],
        ]));

        return $metaModel;
    }

    private function mockRatingAttribute($metaModel, $data)
    {
        $attribute = $this
            ->getMockBuilder(Rating::class)
            ->setConstructorArgs([
                $metaModel,
                $data,
                $this->connection,
                $this->getMockForAbstractClass(RouterInterface::class),
                $this->session,
                $this->getMockBuilder(RequestScopeDeterminator::class)->disableOriginalConstructor()->getMock()
            ])
            ->setMethods(['ensureImage'])
            ->getMock();

        return $attribute;
    }
}
