<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The Formo decorator class
 *
 * @package  Formo
 */
abstract class Formo_Decorator_Core implements Formo_Decorator_Interface {

	/**
	 * The Formo field/form object
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $container;

	/**
	 * Driver object
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $driver;

	/**
	 * Decorator-specific variables
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_vars = array();

	/**
	 * General factory method
	 *
	 * @access public
	 * @static
	 * @param mixed Formo_Container $container
	 * @return void
	 */
	public static function factory(Formo_Container $container, Formo_Driver $driver)
	{
		return new Formo_Decorator($container, $driver);
	}

	/**
	 * Sets $container to the container passed into Formo, also driver
	 *
	 * @access public
	 * @param mixed Formo_Container $container
	 * @return void
	 */
	public function __construct(Formo_Container $container, Formo_Driver $driver)
	{
		$this->container = $container;
		$this->driver = $driver;		
	}
	
	/**
	 * Append event. Run after field is appended to its parent
	 * 
	 * @access public
	 * @return void
	 */
	public function append(){}

}
