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
 * This is the MetaModelAttribute ajax endpoint for the rating attribute.
 *
 * @package    MetaModels
 * @subpackage AttributeRatingAjax
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class MetaModelAttributeRatingAjax
{
	protected function bail($message = 'Invalid AJAX call.')
	{
		// if there is no other output, we generate a 412 error response
		header('HTTP/1.1 400 Bad Request');
		die('Rating Ajax: ' . $message);
	}

	public function handle()
	{
		if ((Input::getInstance()->get('metamodelsattribute_rating')
			&& ($arrData=Input::getInstance()->post('data'))
			&& ($fltValue=Input::getInstance()->post('rating'))
		))
		{
			if (!($arrData && $arrData['id'] && $arrData['pid'] && $arrData['item']))
			{
				$this->bail('Invalid request.');
			}

			$objMetaModel = MetaModelFactory::byId($arrData['pid']);
			if (!$objMetaModel)
			{
				$this->bail('No MetaModel.');
			}

			/**
			 * @var MetaModelAttributeRating $objAttribute
			 */
			$objAttribute = $objMetaModel->getAttributeById($arrData['id']);
			if (!$objAttribute)
			{
				$this->bail('No Attribute.');
			}

			$objAttribute->addVote($arrData['item'], floatval($fltValue), true);

			header('HTTP/1.1 200 Ok');
			exit;
		}
	}
}