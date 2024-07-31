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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Attribute;

use Contao\System;
use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use MetaModels\Attribute\BaseComplex;
use MetaModels\Helper\ToolboxFile;
use MetaModels\IMetaModel;
use MetaModels\Render\Setting\ISimple;
use MetaModels\Render\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This is the MetaModelAttribute class for handling rating fields.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rating extends BaseComplex
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
     * Web session.
     *
     * @var SessionInterface
     */
    private SessionInterface $session;

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
     * Rating constructor.
     *
     * @param IMetaModel                    $objMetaModel      The metamodel to which the attribute belongs to.
     * @param array                         $arrData           Attribute data.
     * @param Connection|null               $connection        Database connection.
     * @param RouterInterface|null          $router            The router.
     * @param SessionInterface|null         $session           Session.
     * @param RequestScopeDeterminator|null $scopeDeterminator Request scope determinator.
     * @param string|null                   $appRoot           The application path.
     * @param string|null                   $webDir            The public web folder.
     * @param RequestStack|null             $requestStack      The Request stack.
     */
    public function __construct(
        IMetaModel $objMetaModel,
        array $arrData = [],
        Connection $connection = null,
        RouterInterface $router = null,
        SessionInterface $session = null,
        RequestScopeDeterminator $scopeDeterminator = null,
        string $appRoot = null,
        string $webDir = null,
        RequestStack $requestStack = null
    ) {
        parent::__construct($objMetaModel, $arrData);

        if (null === $connection) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Connection is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $connection = System::getContainer()->get('database_connection');
            assert($connection instanceof Connection);
        }
        $this->connection = $connection;

        if (null === $router) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Router is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $router = System::getContainer()->get('router');
            assert($router instanceof RouterInterface);
        }
        $this->router = $router;

        if (null === $session) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Not passing an "Session" is deprecated.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $session = System::getContainer()->get('session');
            assert($session instanceof SessionInterface);
        }
        $this->session = $session;

        if (null === $scopeDeterminator) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Scope determinator is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $scopeDeterminator = System::getContainer()->get('cca.dc-general.scope-matcher');
            assert($scopeDeterminator instanceof RequestScopeDeterminator);
        }
        $this->scopeDeterminator = $scopeDeterminator;

        if (null === $appRoot) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'App root is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $appRoot = System::getContainer()->getParameter('kernel.project_dir');
            assert(\is_string($appRoot));
        }
        $this->appRoot = $appRoot;

        if (null === $webDir) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Web dir is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $webDir = '';
        }
        $this->webDir = $webDir;

        if (null === $requestStack) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Request stack is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $requestStack = System::getContainer()->get('request_stack');
            assert($requestStack instanceof RequestStack);
        }
        $this->requestStack = $requestStack;
    }

    /**
     * Returns all valid settings for the attribute type.
     *
     * @return list<string> All valid setting names, this reensembles the columns in tl_metamodel_attribute
     *                      this attribute class understands.
     */
    public function getAttributeSettingNames()
    {
        return \array_merge(
            parent::getAttributeSettingNames(),
            [
                'sortable',
                'rating_half',
                'rating_max',
                'rating_emtpy',
                'rating_full',
                'rating_hover',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        return $varValue['meanvalue'] ?? 0.0;
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        return ['meanvalue' => $varValue];
    }

    /**
     * This generates the field definition for use in a DCA.
     *
     * It also sets the proper language variables (if not already set per dcaconfig.php or similar).
     * Using the optional override parameter, settings known by this attribute can be overridden for the
     * generating of the output array.
     *
     * @param array $arrOverrides The values to override, for a list of valid parameters, call
     *                            getAttributeSettingNames().
     *
     * @return array The DCA array to use as $GLOBALS['TL_DCA']['tablename']['fields']['attribute-name]
     *
     * @codeCoverageIgnore
     */
    public function getFieldDefinition($arrOverrides = [])
    {
        $arrFieldDef              = parent::getFieldDefinition($arrOverrides);
        $arrFieldDef['inputType'] = 'text';
        // We must not change the value.
        $arrFieldDef['eval']['disabled'] = true;

        return $arrFieldDef;
    }

    /**
     * Retrieve the filter options of this attribute.
     *
     * Retrieve values for use in filter options, that will be understood by DC_ filter
     * panels and frontend filter select boxes.
     * One can influence the amount of returned entries with the two parameters.
     * For the id list, the value "null" represents (as everywhere in MetaModels) all entries.
     * An empty array will return no entries at all.
     * The parameter "used only" determines, if only really attached values shall be returned.
     * This is only relevant, when using "null" as id list for attributes that have preconfigured
     * values like select lists and tags i.e.
     *
     * @param list<string>|null $idList   The ids of items that the values shall be fetched from.
     * @param bool              $usedOnly Determines if only "used" values shall be returned.
     * @param array|null        $arrCount Array for the counted values.
     *
     * @return array All options matching the given conditions as name => value.
     *
     * @SuppressWarnings("unused")
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        return [];
    }

    /**
     * Clean up the database.
     *
     * @return void
     */
    public function destroyAUX()
    {
        $this->connection->delete(
            'tl_metamodel_rating',
            [
                'mid' => $this->getMetaModel()->get('id'),
                'aid' => $this->get('id')
            ]
        );
    }

    /**
     * This method is called to retrieve the data for certain items from the database.
     *
     * @param list<string> $arrIds The ids of the items to retrieve.
     *
     * @return array<string, mixed> The nature of the resulting array is a mapping from id => "native data" where
     *                              the definition of "native data" is only of relevance to the given item.
     */
    public function getDataFor($arrIds)
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('tl_metamodel_rating', 't')
            ->andWhere('t.mid=:mid AND t.aid=:aid AND t.iid IN (:iids)')
            ->setParameter('mid', $this->getMetaModel()->get('id'))
            ->setParameter('aid', $this->get('id'))
            ->setParameter('iids', $arrIds, ArrayParameterType::STRING)
            ->executeQuery();

        $arrResult = [];
        while ($objData = $statement->fetchAssociative()) {
            $arrResult[$objData['iid']] = [
                'votecount' => (int) $objData['votecount'],
                'meanvalue' => (float) $objData['meanvalue'],
            ];
        }
        foreach (\array_diff($arrIds, \array_keys($arrResult)) as $intId) {
            $arrResult[$intId] = [
                'votecount' => 0,
                'meanvalue' => 0,
            ];
        }

        return $arrResult;
    }

    /**
     * This method is a no-op in this class.
     *
     * @param array<string, mixed> $arrValues Unused.
     *
     * @return void
     *
     * @SuppressWarnings("unused")
     * @codeCoverageIgnore
     */
    public function setDataFor($arrValues)
    {
        // No op - this attribute is not meant to be manipulated.
    }

    /**
     * Delete all votes for the given items.
     *
     * @param list<string> $arrIds The ids of the items to remove votes for.
     *
     * @return void
     */
    public function unsetDataFor($arrIds)
    {
        $this->connection->createQueryBuilder()
            ->delete('tl_metamodel_rating')
            ->andWhere('tl_metamodel_rating.mid=:mid')
            ->andWhere('tl_metamodel_rating.aid=:aid')
            ->andWhere('tl_metamodel_rating.iid IN (:iids)')
            ->setParameter('mid', $this->getMetaModel()->get('id'))
            ->setParameter('aid', $this->get('id'))
            ->setParameter('iids', $arrIds, ArrayParameterType::STRING)
            ->executeQuery();
    }

    /**
     * Calculate the lock id for a given item.
     *
     * @param string $intItemId The id of the item.
     *
     * @return string
     */
    protected function getLockId($intItemId)
    {
        return \sprintf(
            'vote_lock_%s_%s_%s',
            $this->getMetaModel()->get('id'),
            $this->get('id'),
            $intItemId
        );
    }

    /**
     * Add a vote to the database.
     *
     * @param string $intItemId The id of the item to be voted.
     * @param float  $fltValue  The value of the vote.
     * @param bool   $blnLock   Flag if the user session shall be locked against voting for this item again.
     *
     * @return void
     */
    public function addVote($intItemId, $fltValue, $blnLock = false)
    {
        if ($this->getSessionBag()->get($this->getLockId($intItemId))) {
            return;
        }

        $arrData = $this->getDataFor([$intItemId]);

        if (!$arrData || !$arrData[$intItemId]['votecount']) {
            $voteCount   = 0;
            $prevPercent = 0;
        } else {
            $voteCount   = $arrData[$intItemId]['votecount'];
            $prevPercent = (float) $arrData[$intItemId]['meanvalue'];
        }

        $grandTotal = ($voteCount * $this->get('rating_max') * $prevPercent);
        $hundred    = ($this->get('rating_max') * (++$voteCount));

        // Calculate the percentage.
        $value = (1 / $hundred * ($grandTotal + $fltValue));

        $arrSet = [
            'mid'       => $this->getMetaModel()->get('id'),
            'aid'       => $this->get('id'),
            'iid'       => $intItemId,
            'votecount' => $voteCount,
            'meanvalue' => $value,
        ];

        $queryBuilder = $this->connection->createQueryBuilder();

        if (!$arrData || !$arrData[$intItemId]['votecount']) {
            foreach ($arrSet as $key => $value) {
                $queryBuilder
                    ->setValue($this->connection->quoteIdentifier($key), ':' . $key)
                    ->setParameter($key, $value);
            }

            $queryBuilder
                ->insert('tl_metamodel_rating');
        } else {
            foreach ($arrSet as $key => $value) {
                $queryBuilder
                    ->set($this->connection->quoteIdentifier($key), ':' . $key)
                    ->setParameter($key, $value);
            }

            $queryBuilder
                ->update('tl_metamodel_rating')
                ->andWhere($queryBuilder->expr()->eq($this->connection->quoteIdentifier('mid'), ':mid'))
                ->andWhere($queryBuilder->expr()->eq($this->connection->quoteIdentifier('aid'), ':aid'))
                ->andWhere($queryBuilder->expr()->eq($this->connection->quoteIdentifier('iid'), ':iid'))
                ->setParameter('mid', $this->getMetaModel()->get('id'))
                ->setParameter('aid', $this->get('id'))
                ->setParameter('iid', $intItemId);
        }

        $queryBuilder->executeQuery();

        if ($blnLock) {
            $this->getSessionBag()->set($this->getLockId($intItemId), true);
        }
    }

    /**
     * Test whether the given image exists.
     *
     * @param string $uuidImage  The uuid of the image.
     * @param string $strDefault Path to the fallback image.
     *
     * @return string If the image exists, the image is returned, the default otherwise.
     */
    protected function ensureImage($uuidImage, $strDefault)
    {
        $imagePath = ToolboxFile::convertValueToPath($uuidImage);
        if (\strlen($imagePath) && \file_exists($this->appRoot . '/' . $imagePath)) {
            return $imagePath;
        }

        return $strDefault;
    }

    /**
     * Initialize the template with values.
     *
     * @param Template $objTemplate The Template instance to populate.
     * @param array    $arrRowData  The row data for the current item.
     * @param ISimple  $objSettings The render settings to use for this attribute.
     *
     * @return void
     */
    public function prepareTemplate(Template $objTemplate, $arrRowData, $objSettings)
    {
        parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);

        $strEmpty = $this->ensureImage(
            $this->get('rating_emtpy'),
            'bundles/metamodelsattributerating/star-empty.png'
        );
        $strFull  = $this->ensureImage(
            $this->get('rating_full'),
            'bundles/metamodelsattributerating/star-full.png'
        );
        $strHover = $this->ensureImage(
            $this->get('rating_hover'),
            'bundles/metamodelsattributerating/star-hover.png'
        );

        if (\file_exists($this->appRoot . '/' . $strEmpty)) {
            $size = \getimagesize($this->appRoot . '/' . $strEmpty);
        } elseif (\file_exists($this->webDir . '/' . $strEmpty)) {
            $size = \getimagesize($this->webDir . '/' . $strEmpty);
        } else {
            $size = [0, 0];
        }

        $objTemplate->imageWidth = $size[0];
        $objTemplate->rateHalf   = $this->get('rating_half') ? 'true' : 'false';
        $objTemplate->name       = 'rating_attribute_' . $this->get('id') . '_' . ($arrRowData['id'] ?? 0);

        $objTemplate->ratingDisabled = (
            $this->scopeDeterminator->currentScopeIsBackend()
            || null !== $objSettings->get('rating_disabled')
            || $this->getSessionBag()->get($this->getLockId($arrRowData['id'] ?? '0'))
        );

        $value  = ($this->get('rating_max') * (float) ($arrRowData[$this->getColName()]['meanvalue'] ?? 0));
        $intInc = \strlen($this->get('rating_half')) ? .5 : 1;

        $translator = System::getContainer()->get('translator');
        assert($translator instanceof TranslatorInterface);

        $objTemplate->currentValue = (\round(($value / $intInc), 0) * $intInc);
        $objTemplate->tipText      = $translator->trans(
            'metamodel_rating_label',
            [
                '%value%' => '[VALUE]',
                '%rmax%' => $this->get('rating_max')
            ],
            'tl_metamodel_rendersetting'
        );

        $objTemplate->ajaxUrl  = $this->router->generate('metamodels.attribute_rating.rate');
        $objTemplate->ajaxData = \json_encode(
            [
                'id'   => ($this->get('id') ?? 0),
                'pid'  => $this->get('pid'),
                'item' => ($arrRowData['id'] ?? 0),
            ]
        );

        $arrOptions = [];
        $intValue   = $intInc;

        while ($intValue <= $this->get('rating_max')) {
            $arrOptions[] = $intValue;
            $intValue    += $intInc;
        }

        $request = $this->requestStack->getCurrentRequest();
        assert($request instanceof Request);
        $objTemplate->options    = $arrOptions;
        $objTemplate->imageEmpty = $request->getUriForPath('/' . $strEmpty);
        $objTemplate->imageFull  = $request->getUriForPath('/' . $strFull);
        $objTemplate->imageHover = $request->getUriForPath('/' . $strHover);
    }

    /**
     * Sorts the given array list by field value in the given direction.
     *
     * @param list<string> $idList A list of Ids from the MetaModel table.
     * @param string       $strDirection The direction for sorting. either 'ASC' or 'DESC', as in plain SQL.
     *
     * @return list<string> The sorted integer array.
     *
     * @throws Exception
     */
    public function sortIds($idList, $strDirection)
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('t.iid')
            ->from('tl_metamodel_rating', 't')
            ->andWhere('t.mid=:mid AND t.aid=:aid AND t.iid IN (:iids)')
            ->setParameter('mid', $this->getMetaModel()->get('id'))
            ->setParameter('aid', $this->get('id'))
            ->setParameter('iids', $idList, ArrayParameterType::STRING)
            ->orderBy('t.meanvalue', $strDirection)
            ->addOrderBy('t.votecount', $strDirection)
            ->executeQuery();

        $arrSorted = $statement->fetchFirstColumn();

        return ($strDirection === 'DESC')
            ? \array_merge($arrSorted, \array_diff($idList, $arrSorted))
            : \array_merge(\array_diff($idList, $arrSorted), $arrSorted);
    }

    /**
     * Returns the language array of the actual language (replacement for super globals access).
     *
     * @return array The language Strings
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function getActiveLanguageArray()
    {
        return $GLOBALS['TL_LANG'];
    }

    /**
     * Get the session bag depending on current scope.
     *
     * @return AttributeBagInterface
     */
    protected function getSessionBag()
    {
        if ($this->scopeDeterminator->currentScopeIsBackend()) {
            $sessionBag = $this->session->getBag('contao_backend');
            assert($sessionBag instanceof AttributeBagInterface);

            return $sessionBag;
        }

        if ($this->scopeDeterminator->currentScopeIsFrontend()) {
            $sessionBag = $this->session->getBag('contao_frontend');
            assert($sessionBag instanceof AttributeBagInterface);

            return $sessionBag;
        }

        $sessionBag = $this->session->getBag('attributes');
        assert($sessionBag instanceof AttributeBagInterface);

        return $sessionBag;
    }
}
