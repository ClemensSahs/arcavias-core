<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog detail basket section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Basket_Attribute_Default
	extends Client_Html_Abstract
{
	private $_cache;
	private $_subPartNames = array();
	private $_subPartPath = 'client/html/catalog/detail/basket/attribute/default/subparts';


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->attributeBody = $html;

		$tplconf = 'client/html/catalog/detail/basket/attribute/default/template-body';
		$default = 'catalog/detail/basket-attribute-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->attributeHeader = $html;

		$tplconf = 'client/html/catalog/detail/basket/attribute/default/template-header';
		$default = 'catalog/detail/basket-attribute-header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'catalog/detail/basket/attribute/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return $this->_isCachable( $what, $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$attributeTypes = array();
			$attrItems = $view->get( 'detailProductAttributeItems', array() );
			$configAttributes = $view->detailProductItem->getRefItems( 'attribute', null, 'config' );

			foreach( $configAttributes as $id => $attribute )
			{
				if( isset( $attrItems[$id] ) ) {
					$attributeTypes[ $attrItems[$id]->getType() ][$id] = $attrItems[$id];
				}
			}

			$view->attributeConfigItems = $attributeTypes;
			$view->attributeHiddenItems = $view->detailProductItem->getRefItems( 'attribute', null, 'hidden' );

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}