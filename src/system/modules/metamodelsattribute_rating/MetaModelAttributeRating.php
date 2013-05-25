<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeRating
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * This is the MetaModelAttribute class for handling numeric fields.
 *
 * @package    MetaModels
 * @subpackage AttributeRating
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class MetaModelAttributeRating extends MetaModelAttributeComplex
{
	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'sortable',
			'rating_half',
			'rating_max',
			'rating_emtpy',
			'rating_full',
			'rating_hover',
		));
	}

	public function getFieldDefinition($arrOverrides = array())
	{
		$arrFieldDef=parent::getFieldDefinition($arrOverrides);
		$arrFieldDef['inputType'] = 'submit';
		return $arrFieldDef;
	}

	public function getFilterOptions($arrIds, $usedOnly, &$arrCount = null)
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
		Database::getInstance()
			->prepare('DELETE FROM tl_metamodel_rating WHERE mid=? AND aid=?')
			->execute($this->getMetaModel()->get('id'), $this->get('id'));
	}

	public function getDataFor($arrIds)
	{
		$objData = Database::getInstance()
			->prepare(sprintf(
				'SELECT * FROM tl_metamodel_rating WHERE (mid=?) AND (aid=?) AND (iid IN (%s))',
				implode(', ', array_fill(0, count($arrIds), '?'))
			))
			->execute(array_merge(array
				(
					$this->getMetaModel()->get('id'),
					$this->get('id')
				),
				$arrIds
			));

		$arrResult = array();
		while ($objData->next())
		{
			$arrResult[$objData->iid] = array
			(
				'votecount' => $objData->votecount,
				'meanvalue' => $objData->meanvalue,
			);
		}
		foreach (array_diff($arrIds, array_keys($arrResult)) as $intId)
		{
			$arrResult[$intId] = array
			(
				'votecount' => 0,
				'meanvalue' => 0,
			);
		}

		return $arrResult;
	}

	public function setDataFor($arrValues)
	{
		// No op - this attribute is not meant to be manipulated.
	}

	/**
	 * Delete all votes for the given items.
	 *
	 * @param int[] $arrIds
	 *
	 * @return void
	 */
	public function unsetDataFor($arrIds)
	{
		Database::getInstance()
			->prepare(sprintf(
				'DELETE FROM tl_metamodel_rating WHERE mid=? AND aid=? AND (iid IN (%s))',
				implode(', ', array_fill(0, count($arrIds), '?'))))
			->execute(array_merge(array
				(
					$this->getMetaModel()->get('id'),
					$this->get('id')
				),
				$arrIds
			));
	}

	protected function getLockId($intItemId)
	{
		return sprintf('vote_lock_%s_%s_%s',
			$this->getMetaModel()->get('id'),
			$this->get('id'),
			$intItemId
		);
	}

	public function addVote($intItemId, $fltValue, $blnLock = false)
	{
		if (Session::getInstance()->get($this->getLockId($intItemId)))
		{
			return;
		}

		$arrData = $this->getDataFor(array($intItemId));

		if (!$arrData || !$arrData[$intItemId]['votecount'])
		{
			$voteCount = 0;
			$prevPercent = 0;
		}
		else
		{
			$voteCount = $arrData[$intItemId]['votecount'];
			$prevPercent = floatval($arrData[$intItemId]['meanvalue']);
		}

		$grandTotal = $voteCount*$this->get('rating_max')*$prevPercent;
		$hundred = $this->get('rating_max')*(++$voteCount);

		// calculate the percentage.
		$value = 1/$hundred*($grandTotal+$fltValue);

		$arrSet = array
		(
			'mid' => $this->getMetaModel()->get('id'),
			'aid' => $this->get('id'),
			'iid' => $intItemId,
			'votecount' => $voteCount,
			'meanvalue' => $value,
		);

		if (!$arrData || !$arrData[$intItemId]['votecount'])
		{
			$strSQL = 'INSERT INTO tl_metamodel_rating %s';
		}
		else
		{
			$strSQL = 'UPDATE tl_metamodel_rating %s WHERE mid=? AND aid=? AND iid=?';
		}

		Database::getInstance()
			->prepare($strSQL)
			->set($arrSet)
			->execute(
				$this->getMetaModel()->get('id'),
				$this->get('id'),
				$intItemId
			);

		if ($blnLock)
		{
			Session::getInstance()->set($this->getLockId($intItemId), true);
		}
	}

	public function prepareTemplate(MetaModelTemplate $objTemplate, $arrRowData, $objSettings = null)
	{
		parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);

		$base = Environment::getInstance()->base;

		$strEmpty = strlen($this->get('rating_emtpy')) && file_exists(TL_ROOT . '/' . $this->get('rating_emtpy'))
			? $this->get('rating_emtpy')
			: 'system/modules/metamodelsattribute_rating/html/star-empty.png';

		$strFull = strlen($this->get('rating_full')) && file_exists(TL_ROOT . '/' . $this->get('rating_full'))
			? $this->get('rating_full')
			: 'system/modules/metamodelsattribute_rating/html/star-full.png';

		$strHover = strlen($this->get('rating_hover')) && file_exists(TL_ROOT . '/' . $this->get('rating_hover'))
			? $this->get('rating_hover')
			: 'system/modules/metamodelsattribute_rating/html/star-hover.png';

		$size = getimagesize(TL_ROOT . '/' . $strEmpty);
		$objTemplate->imageWidth = $size[0];
		$objTemplate->rateHalf = $this->get('rating_half') ? 'true' : 'false';
		$objTemplate->name = 'rating_attribute_'.$this->get('id') . '_' . $arrRowData['id'];

		$objTemplate->ratingDisabled = (
			(TL_MODE == 'BE')
			|| $objSettings->rating_disabled
			|| Session::getInstance()->get($this->getLockId($arrRowData['id']))
		);

		$value = $this->get('rating_max')*floatval($arrRowData[$this->getColName()]['meanvalue']);

		$objTemplate->currentValue = round($value/.5, 0)*.5;

		$objTemplate->tipText = sprintf($GLOBALS['TL_LANG']['metamodel_rating_label'], '[VALUE]', $this->get('rating_max'));

		$objTemplate->ajaxUrl = sprintf('SimpleAjax.php?metamodelsattribute_rating=%s', $this->get('id'));
		$objTemplate->ajaxData = json_encode(array
			(
				'id' => $this->get('id'),
				'pid' => $this->get('pid'),
				'item' => $arrRowData['id']
			)
		);

		$arrOptions = array();
		$intInc = $objTemplate->rateHalf ? .5 : 1;
		$intValue = $intInc;
		while($intValue<=$this->get('rating_max'))
		{
			$arrOptions[] = $intValue;
			$intValue += $intInc;
		}
		$objTemplate->options = $arrOptions;

		$objTemplate->imageEmpty = $base . $strEmpty;
		$objTemplate->imageFull  = $base . $strFull;
		$objTemplate->imageHover = $base . $strHover;
	}

	public function sortIds($arrIds, $strDirection)
	{
		$objData = Database::getInstance()
			->prepare(sprintf(
				'SELECT iid FROM tl_metamodel_rating WHERE (mid=?) AND (aid=?) AND (iid IN (%s)) ORDER BY meanvalue ' . $strDirection,
				implode(', ', array_fill(0, count($arrIds), '?'))
			))
			->execute(array_merge(array
				(
					$this->getMetaModel()->get('id'),
					$this->get('id')
				),
				$arrIds
			));
		$arrSorted = $objData->fetchEach('iid');

		return ($strDirection == 'DESC')
			? array_merge($arrSorted, array_diff($arrIds, $arrSorted))
			: array_merge(array_diff($arrIds, $arrSorted), $arrSorted);
	}
}