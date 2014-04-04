<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout payment order HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Order_Payment_Default
	extends Client_Html_Abstract
{
	/** client/html/checkout/standard/order/payment/default/subparts
	 * List of HTML sub-clients rendered within the checkout standard order payment section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartPath = 'client/html/checkout/standard/order/payment/default/subparts';
	private $_subPartNames = array();
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->paymentBody = $html;

		/** client/html/checkout/standard/order/payment/default/template-body
		 * Relative path to the HTML body template of the checkout standard order payment client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the layouts directory (usually in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/order/payment/default/template-header
		 */
		$tplconf = 'client/html/checkout/standard/order/payment/default/template-body';
		$default = 'checkout/standard/order-payment-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->paymentHeader = $html;

		/** client/html/checkout/standard/order/payment/default/template-header
		 * Relative path to the HTML header template of the checkout standard order payment client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the layouts directory (usually
		 * in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/order/payment/default/template-body
		 */
		$tplconf = 'client/html/checkout/standard/order/payment/default/template-header';
		$default = 'checkout/standard/order-payment-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/order/payment/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return false;
	}


	/**
	 * Processes the input, e.g. provides the payment form.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();
		$basket = $view->orderBasket;
		$orderItem = $view->orderItem;
		$context = $this->_getContext();


		/** client/html/checkout/confirm/url/target
		 * Destination of the URL where the controller specified in the URL is known
		 *
		 * The destination can be a page ID like in a content management system or the
		 * module of a software development framework. This "target" must contain or know
		 * the controller that should be called by the generated URL.
		 *
		 * @param string Destination of the URL
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/controller
		 * @see client/html/checkout/confirm/url/action
		 * @see client/html/checkout/confirm/url/config
		 */
		$target = $view->config( 'client/html/checkout/confirm/url/target' );

		/** client/html/checkout/confirm/url/controller
		 * Name of the controller whose action should be called
		 *
		 * In Model-View-Controller (MVC) applications, the controller contains the methods
		 * that create parts of the output displayed in the generated HTML page. Controller
		 * names are usually alpha-numeric.
		 *
		 * @param string Name of the controller
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/target
		 * @see client/html/checkout/confirm/url/action
		 * @see client/html/checkout/confirm/url/config
		 */
		$controller = $view->config( 'client/html/checkout/confirm/url/controller', 'checkout' );

		/** client/html/checkout/confirm/url/action
		 * Name of the action that should create the output
		 *
		 * In Model-View-Controller (MVC) applications, actions are the methods of a
		 * controller that create parts of the output displayed in the generated HTML page.
		 * Action names are usually alpha-numeric.
		 *
		 * @param string Name of the action
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/target
		 * @see client/html/checkout/confirm/url/controller
		 * @see client/html/checkout/confirm/url/config
		 */
		$action = $view->config( 'client/html/checkout/confirm/url/action', 'confirm' );

		/** client/html/checkout/confirm/url/config
		 * Associative list of configuration options used for generating the URL
		 *
		 * You can specify additional options as key/value pairs used when generating
		 * the URLs, like
		 *
		 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
		 *
		 * The available key/value pairs depend on the application that embeds the e-commerce
		 * framework. This is because the infrastructure of the application is used for
		 * generating the URLs. The full list of available config options is referenced
		 * in the "see also" section of this page.
		 *
		 * @param string Associative list of configuration options
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/target
		 * @see client/html/checkout/confirm/url/controller
		 * @see client/html/checkout/confirm/url/action
		 * @see client/html/url/config
		 */
		$config = $view->config( 'client/html/checkout/confirm/url/config', array( 'absoluteUri' => true ) );

		$confirmUrl = $view->url( $target, $controller, $action, array(), array(), $config );


		/** client/html/checkout/update/url/target
		 * Destination of the URL where the controller specified in the URL is known
		 *
		 * The destination can be a page ID like in a content management system or the
		 * module of a software development framework. This "target" must contain or know
		 * the controller that should be called by the generated URL.
		 *
		 * @param string Destination of the URL
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/controller
		 * @see client/html/checkout/update/url/action
		 * @see client/html/checkout/update/url/config
		 */
		$target = $view->config( 'client/html/checkout/update/url/target' );

		/** client/html/checkout/update/url/controller
		 * Name of the controller whose action should be called
		 *
		 * In Model-View-Controller (MVC) applications, the controller contains the methods
		 * that create parts of the output displayed in the generated HTML page. Controller
		 * names are usually alpha-numeric.
		 *
		 * @param string Name of the controller
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/target
		 * @see client/html/checkout/update/url/action
		 * @see client/html/checkout/update/url/config
		 */
		$controller = $view->config( 'client/html/checkout/update/url/controller', 'checkout' );

		/** client/html/checkout/update/url/action
		 * Name of the action that should create the output
		 *
		 * In Model-View-Controller (MVC) applications, actions are the methods of a
		 * controller that create parts of the output displayed in the generated HTML page.
		 * Action names are usually alpha-numeric.
		 *
		 * @param string Name of the action
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/target
		 * @see client/html/checkout/update/url/controller
		 * @see client/html/checkout/update/url/config
		 */
		$action = $view->config( 'client/html/checkout/update/url/action', 'update' );

		/** client/html/checkout/update/url/config
		 * Associative list of configuration options used for generating the URL
		 *
		 * You can specify additional options as key/value pairs used when generating
		 * the URLs, like
		 *
		 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
		 *
		 * The available key/value pairs depend on the application that embeds the e-commerce
		 * framework. This is because the infrastructure of the application is used for
		 * generating the URLs. The full list of available config options is referenced
		 * in the "see also" section of this page.
		 *
		 * @param string Associative list of configuration options
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/target
		 * @see client/html/checkout/update/url/controller
		 * @see client/html/checkout/update/url/action
		 * @see client/html/url/config
		 */
		$config = $view->config( 'client/html/checkout/update/url/config', array( 'absoluteUri' => true ) );

		$updateUrl = $view->url( $target, $controller, $action, array(), array(), $config );


		$config = array( 'payment.url-success' => $confirmUrl, 'payment.url-update' => $updateUrl );


		try
		{
			$service = $basket->getService( 'payment' );

			$manager = MShop_Service_Manager_Factory::createManager( $context );
			$provider = $manager->getProvider( $manager->getItem( $service->getServiceId() ) );
			$provider->injectGlobalConfigBE( $config );

			$view->paymentForm = $provider->process( $orderItem );
		}
		catch( MShop_Order_Exception $e )
		{
			$view->paymentForm = new MShop_Common_Item_Helper_Form_Default( $confirmUrl, 'REDIRECT' );
		}

		if( !isset( $view->paymentForm ) || $view->paymentForm === null )
		{
			$msg = sprintf( 'Invalid process response from service provider with code "%1$s"', $service->getCode() );
			throw new Client_Html_Exception( $msg );
		}

		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}