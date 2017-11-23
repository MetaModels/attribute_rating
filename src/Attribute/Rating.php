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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeRatingBundle\Attribute;

use Contao\Environment;
use Contao\System;
use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\BaseComplex;
use MetaModels\Helper\ToolboxFile;
use MetaModels\IMetaModel;
use MetaModels\Render\Setting\ISimple;
use MetaModels\Render\Template;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * This is the MetaModelAttribute class for handling numeric fields.
 *
 * @package    MetaModels
 * @subpackage AttributeRating
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class Rating extends BaseComplex
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
     * Web session.
     *
     * @var null|SessionInterface
     */
    private $session;

    /**
     * Request scope determinator.
     *
     * @var RequestScopeDeterminator|null
     */
    private $scopeDeterminator;

    /**
     * Rating constructor.
     *
     * @param IMetaModel                    $objMetaModel      The metamodel to which the attribute belongs to.
     * @param array                         $arrData           Attribute data.
     * @param Connection|null               $connection        Database connection.
     * @param RouterInterface|null          $router            The router.
     * @param SessionInterface|null         $session           Session.
     * @param RequestScopeDeterminator|null $scopeDeterminator Request scope determinator.
     */
    public function __construct(
        IMetaModel $objMetaModel,
        array $arrData = [],
        Connection $connection = null,
        RouterInterface $router = null,
        SessionInterface $session = null,
        RequestScopeDeterminator $scopeDeterminator = null
    ) {
        parent::__construct($objMetaModel, $arrData);

        // @codingStandardsIgnoreStart Silencing errors is discouraged
        if (null === $connection) {
            @trigger_error(
                'Connection is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            $connection = System::getContainer()->get('database_connection');
        }

        if (null === $router) {
            @trigger_error(
                'Router is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );

            $router = System::getContainer()->get('router');
        }

        if (null === $session) {
            @trigger_error(
                'Router is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );

            $session = System::getContainer()->get('session');
        }

        if (null === $scopeDeterminator) {
            @trigger_error(
                'Scope determinator is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );

            $scopeDeterminator = System::getContainer()->get('cca.dc-general.scope-matcher');
        }
        // @codingStandardsIgnoreEnd

        $this->connection        = $connection;
        $this->router            = $router;
        $this->session           = $session;
        $this->scopeDeterminator = $scopeDeterminator;
    }

    /**
     * Returns all valid settings for the attribute type.
     *
     * @return array All valid setting names, this reensembles the columns in tl_metamodel_attribute
     *               this attribute class understands.
     */
    public function getAttributeSettingNames()
    {
        return array_merge(
            parent::getAttributeSettingNames(),
            array(
                'sortable',
                'rating_half',
                'rating_max',
                'rating_emtpy',
                'rating_full',
                'rating_hover',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        return $varValue['meanvalue'];
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
    public function getFieldDefinition($arrOverrides = array())
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
     * @param array $idList   The ids of items that the values shall be fetched from.
     * @param bool  $usedOnly Determines if only "used" values shall be returned.
     * @param bool  $arrCount Array for the counted values.
     *
     * @return array All options matching the given conditions as name => value.
     *
     * @SuppressWarnings("unused")
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        return array();
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
     * @param int[] $arrIds The ids of the items to retrieve.
     *
     * @return mixed[] The nature of the resulting array is a mapping from id => "native data" where
     *                 the definition of "native data" is only of relevance to the given item.
     */
    public function getDataFor($arrIds)
    {
        $query     = 'SELECT * FROM tl_metamodel_rating WHERE (mid=:mid) AND (aid=:aid) AND (iid IN (:iids))';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('mid', $this->getMetaModel()->get('id'));
        $statement->bindValue('aid', $this->get('id'));
        $statement->bindValue('iids', $arrIds, Connection::PARAM_INT_ARRAY);
        $statement->execute();

        $arrResult = array();
        while ($objData = $statement->fetch(\PDO::FETCH_OBJ)) {
            $arrResult[$objData->iid] = array(
                'votecount' => intval($objData->votecount),
                'meanvalue' => floatval($objData->meanvalue),
            );
        }
        foreach (array_diff($arrIds, array_keys($arrResult)) as $intId) {
            $arrResult[$intId] = array(
                'votecount' => 0,
                'meanvalue' => 0,
            );
        }

        return $arrResult;
    }

    /**
     * This method is a no-op in this class.
     *
     * @param mixed[int] $arrValues Unused.
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
     * @param int[] $arrIds The ids of the items to remove votes for.
     *
     * @return void
     */
    public function unsetDataFor($arrIds)
    {
        $query     = 'DELETE FROM tl_metamodel_rating WHERE mid=:mid AND aid=:aid AND (iid IN (:iids))';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('mid', $this->getMetaModel()->get('id'));
        $statement->bindValue('aid', $this->get('id'));
        $statement->bindValue('iids', $arrIds, Connection::PARAM_INT_ARRAY);
        $statement->execute();
    }

    /**
     * Calculate the lock id for a given item.
     *
     * @param int $intItemId The id of the item.
     *
     * @return string
     */
    protected function getLockId($intItemId)
    {
        return sprintf(
            'vote_lock_%s_%s_%s',
            $this->getMetaModel()->get('id'),
            $this->get('id'),
            $intItemId
        );
    }

    /**
     * Add a vote to the database.
     *
     * @param int   $intItemId The id of the item to be voted.
     * @param float $fltValue  The value of the vote.
     * @param bool  $blnLock   Flag if the user session shall be locked against voting for this item again.
     *
     * @return void
     */
    public function addVote($intItemId, $fltValue, $blnLock = false)
    {
        if ($this->getSessionBag()->get($this->getLockId($intItemId))) {
            return;
        }

        $arrData = $this->getDataFor(array($intItemId));

        if (!$arrData || !$arrData[$intItemId]['votecount']) {
            $voteCount   = 0;
            $prevPercent = 0;
        } else {
            $voteCount   = $arrData[$intItemId]['votecount'];
            $prevPercent = floatval($arrData[$intItemId]['meanvalue']);
        }

        $grandTotal = ($voteCount * $this->get('rating_max') * $prevPercent);
        $hundred    = ($this->get('rating_max') * (++$voteCount));

        // Calculate the percentage.
        $value = (1 / $hundred * ($grandTotal + $fltValue));

        $arrSet = array(
            'mid' => $this->getMetaModel()->get('id'),
            'aid' => $this->get('id'),
            'iid' => $intItemId,
            'votecount' => $voteCount,
            'meanvalue' => $value,
        );

        $queryBuilder = $this->connection->createQueryBuilder();

        if (!$arrData || !$arrData[$intItemId]['votecount']) {
            $queryBuilder
                ->insert('tl_metamodel_rating')
                ->values($arrSet);
        } else {
            foreach ($arrSet as $key => $value) {
                $queryBuilder
                    ->set($key, ':' . $key)
                    ->setParameter($key, $value);
            }
            
            $queryBuilder
                ->update('tl_metamodel_rating')
                ->andWhere('mid=:mid AND aid=:aid AND iid=:iid')
                ->setParameter('mid', $this->getMetaModel()->get('id'))
                ->setParameter('aid', $this->get('id'))
                ->setParameter('iid', $intItemId);
        }

        $queryBuilder->execute();

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
        if (strlen($imagePath) && file_exists(TL_ROOT . '/' . $imagePath)) {
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

        $base = Environment::get('base');
        $lang = $this->getActiveLanguageArray();

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

        if (file_exists(TL_ROOT . '/' . $strEmpty)) {
            $size = getimagesize(TL_ROOT.'/'. $strEmpty);
        } else {
            $size = getimagesize(TL_ROOT.'/web/'. $strEmpty);
        }

        $objTemplate->imageWidth = $size[0];
        $objTemplate->rateHalf   = $this->get('rating_half') ? 'true' : 'false';
        $objTemplate->name       = 'rating_attribute_'.$this->get('id').'_'.$arrRowData['id'];

        $objTemplate->ratingDisabled = (
            (TL_MODE == 'BE')
            || $objSettings->get('rating_disabled')
            || $this->getSessionBag()->get($this->getLockId($arrRowData['id']))
        );

        $value = ($this->get('rating_max') * floatval($arrRowData[$this->getColName()]['meanvalue']));

        $objTemplate->currentValue = (round(($value / .5), 0) * .5);
        $objTemplate->tipText      = sprintf(
            $lang['metamodel_rating_label'],
            '[VALUE]',
            $this->get('rating_max')
        );
        $objTemplate->ajaxUrl      = $this->router->generate('metamodels.attribute_rating.rate');
        $objTemplate->ajaxData     = json_encode(
            array(
                'id' => $this->get('id'),
                'pid' => $this->get('pid'),
                'item' => $arrRowData['id'],
            )
        );

        $arrOptions = array();
        $intInc     = strlen($this->get('rating_half')) ? .5 : 1;
        $intValue   = $intInc;

        while ($intValue <= $this->get('rating_max')) {
            $arrOptions[] = $intValue;
            $intValue    += $intInc;
        }
        $objTemplate->options = $arrOptions;

        $objTemplate->imageEmpty = $base.$strEmpty;
        $objTemplate->imageFull  = $base.$strFull;
        $objTemplate->imageHover = $base.$strHover;
    }

    /**
     * Sorts the given array list by field value in the given direction.
     *
     * @param int[]  $idList       A list of Ids from the MetaModel table.
     * @param string $strDirection The direction for sorting. either 'ASC' or 'DESC', as in plain SQL.
     *
     * @return int[] The sorted integer array.
     */
    public function sortIds($idList, $strDirection)
    {
        $query = '
            SELECT   iid 
            FROM     tl_metamodel_rating 
            WHERE    (mid=:mid) AND (aid=:aid) AND (iid IN (:iids)) 
            ORDER BY meanvalue ' . $strDirection;

        $statement = $this->connection->prepare($query);
        $statement->bindValue('mid', $this->getMetaModel()->get('id'));
        $statement->bindValue('aid', $this->get('id'));
        $statement->bindValue('iids', $idList, Connection::PARAM_INT_ARRAY);
        $statement->execute();

        $arrSorted = $statement->fetchAll(\PDO::FETCH_COLUMN, 'iid');

        return ($strDirection == 'DESC')
            ? array_merge($arrSorted, array_diff($idList, $arrSorted))
            : array_merge(array_diff($idList, $arrSorted), $arrSorted);
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
     * @return AttributeBagInterface|SessionBagInterface
     */
    protected function getSessionBag()
    {
        if ($this->scopeDeterminator->currentScopeIsBackend()) {
            return $this->session->getBag('contao_backend');
        }

        if ($this->scopeDeterminator->currentScopeIsFrontend()) {
            return $this->session->getBag('contao_frontend');
        }

        return $this->session->getBag('attributes');
    }
}
