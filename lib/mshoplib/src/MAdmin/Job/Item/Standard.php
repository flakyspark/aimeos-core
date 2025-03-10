<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MAdmin
 * @subpackage Job
 */


namespace Aimeos\MAdmin\Job\Item;


/**
 * Default job item implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MAdmin\Job\Item\Iface
{
	/**
	 * Initializes the job item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'job.', $values );
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getLabel()
	{
		return (string) $this->get( 'job.label', '' );
	}


	/**
	 * Sets the new label of the job item.
	 *
	 * @param string $label Type label of the job item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setLabel( $label )
	{
		return $this->set( 'job.label', (string) $label );
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getMethod()
	{
		return (string) $this->get( 'job.method', '' );
	}


	/**
	 * Sets the new method for the job.
	 *
	 * @param string $method Method (object/methodname) to call
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setMethod( $method )
	{
		return $this->set( 'job.method', (string) $method );
	}


	/**
	 * Returns the parameter for the job.
	 *
	 * @return array Parameter of the job
	 */
	public function getParameter()
	{
		return (array) $this->get( 'job.parameter', [] );
	}


	/**
	 * Sets the new parameter for the job.
	 *
	 * @param array $param Parameter for the job
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setParameter( array $param )
	{
		return $this->set( 'job.parameter', $param );
	}


	/**
	 * Returns the result of the job.
	 *
	 * @return array Associative list of result key/value pairs or list thereof
	 */
	public function getResult()
	{
		return (array) $this->get( 'job.result', [] );
	}


	/**
	 * Sets the new result of the job.
	 *
	 * @param array $result Associative list of result key/value pairs or list thereof
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setResult( array $result )
	{
		return $this->set( 'job.result', $result );
	}


	/**
	 * Returns the status (enabled/disabled) of the job item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'job.status', 1 );
	}


	/**
	 * Sets the new status of the job item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'job.status', (int) $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'job';
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'job.label': $item = $item->setLabel( $value ); break;
				case 'job.method': $item = $item->setMethod( $value ); break;
				case 'job.parameter': $item = $item->setParameter( $value ); break;
				case 'job.result': $item = $item->setResult( $value ); break;
				case 'job.status': $item = $item->setStatus( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['job.label'] = $this->getLabel();
		$list['job.method'] = $this->getMethod();
		$list['job.parameter'] = $this->getParameter();
		$list['job.result'] = $this->getResult();
		$list['job.status'] = $this->getStatus();

		return $list;
	}

}
