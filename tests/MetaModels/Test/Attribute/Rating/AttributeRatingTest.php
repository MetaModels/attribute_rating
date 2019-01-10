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
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Test\Attribute\Rating;

use Contao\Database;
use Contao\Session;
use CyberSpectrum\TestHarness\Reflector;
use MetaModels\Attribute\Rating\Rating;
use MetaModels\Factory;
use MetaModels\Test\TestCase;

/**
 * Test the rating attribute.
 */
class AttributeRatingTest extends TestCase
{
    /**
     * Prepare the database.
     *
     * @return bool
     */
    protected function prepareDb()
    {
        $this->markTestSkipped('Currently this does not work anymore.');
        return;

        $this->installIntoContao(
            'src/system/modules/metamodelsattribute_rating/config',
            'TL_ROOT/system/modules/metamodelsattribute_rating'
        );
        $this->installIntoContao(
            'src/system/modules/metamodelsattribute_rating/dca',
            'TL_ROOT/system/modules/metamodelsattribute_rating'
        );
        $this->installIntoContao(
            'src/system/modules/metamodelsattribute_rating/html',
            'TL_ROOT/system/modules/metamodelsattribute_rating'
        );

        $this->installIntoContao(
            'src/system/modules/metamodelsattribute_rating/languages/en',
            'TL_ROOT/system/modules/metamodelsattribute_rating/languages'
        );

        if (!$this->bootContao()) {
            $this->markTestSkipped('Contao not correctly initialized.');

            return false;
        }

        if (!$this->connectDatabase()) {
            $this->markTestSkipped('Contao Database not correctly initialized.');

            return false;
        }

        $worker = $this->getDbWorker();

        $this->assertNotEmpty($worker);

        $worker->createSchema();

        $worker->importData('test/data/testcases.sql');

        return true;
    }

    /**
     * Test the retrieval of a vote.
     *
     * @return void
     */
    public function testFetchVote()
    {
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

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
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

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
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(1, 10);

        $this->assertEquals(
            [
                1 => [
                    'votecount' => 2,
                    'meanvalue' => 1.0,
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
    public function testCastVoteHalf()
    {
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

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
    public function testCastUnsetDataFor()
    {
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

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
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        Session::getInstance()->set(Reflector::invoke($rating, 'getLockId', 1), true);

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
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(1, 10, true);

        $rating->addVote(1, 0, true);

        $this->assertEquals(
            [
                1 => [
                    'votecount' => 2,
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
    public function testCastVoteForNewItem()
    {
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

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
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->addVote(2, 10);
        $rating->addVote(3, 5);

        $this->assertEquals(
            [3, 2],
            $rating->sortIds([2, 3], 'ASC')
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
    public function testEnsureImageExisting()
    {
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $this->assertEquals(
            'system/modules/metamodelsattribute_rating/html/star-empty.png',
            Reflector::invoke($rating, 'ensureImage',
                'system/modules/metamodelsattribute_rating/html/star-empty.png',
                ''
            )
        );

        $this->assertEquals(
            'fallback',
            Reflector::invoke($rating, 'ensureImage',
                'does/not/exist',
                'fallback'
            )
        );
    }

    /**
     * Test the ensure image method.
     *
     * @return void
     */
    public function testPrepareTemplate()
    {
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating   = $metamodel->getAttribute('rating');
        $item     = $metamodel->findById(1);
        $itemData = Reflector::getPropertyValue($item, 'arrData');
        $template = new MetaModelTemplate();
        $settings = $rating->getDefaultRenderSettings();

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
     * Test the getFilterOptions() method.
     *
     * @return void
     */
    public function testGetfilterOptions()
    {
        $this->markTestIncomplete();

        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating   = $metamodel->getAttribute('rating');

        // TODO: fill with code when getFilterOptions() is implemented.
        $this->assertEquals(
            [],
            $rating->getFilterOptions([1], false)
        );

        $this->assertEquals(
            [],
            $rating->getFilterOptions([1], true)
        );

        $this->assertEquals(
            [],
            $rating->getFilterOptions(null, false)
        );

        $this->assertEquals(
            [],
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
        // This marks the test skipped upon error as Contao related testing is not available.
        if (!$this->prepareDb()) {
            return;
        }

        $metamodel = Factory::byTableName('mm_movies');

        /** @var Rating $rating */
        $rating = $metamodel->getAttribute('rating');

        $rating->destroyAUX();

        $query1 = Database::getInstance()->execute('SELECT * FROM tl_metamodel_rating WHERE mid=1 AND aid=1');

        $this->assertEquals(
            0,
            $query1->numRows
        );

        $this->assertEquals(
            [],
            $query1->fetchAllAssoc()
        );

        // Ensure the data from the other attribute is still present.
        $query2 = Database::getInstance()->execute('SELECT * FROM tl_metamodel_rating WHERE mid=1 AND aid=2');

        $this->assertEquals(
            1,
            $query2->numRows
        );

        $this->assertEquals(
            [
                [
                    'id'        => 2,
                    'mid'       => 1,
                    'aid'       => 2,
                    'iid'       => 1,
                    'votecount' => 1,
                    'meanvalue' => 1.0,
                ],
            ],
            $query2->fetchAllAssoc()
        );
    }
}
