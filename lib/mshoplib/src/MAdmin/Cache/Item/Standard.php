<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MAdmin
 * @subpackage Cache
 */


namespace Aimeos\MAdmin\Cache\Item;


/**
 * Default cache item implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MAdmin\Cache\Item\Iface
{
	/**
	 * Initializes the log item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'cache.', $values );
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId()
	{
		return $this->get( 'id' );
	}


	/**
	 * Sets the unique ID of the item.
	 *
	 * @param string $id Unique ID of the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setId( $id )
	{
		return $this->set( 'id', $id );
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		return $this->get( 'siteid' );
	}


	/**
	 * Returns the value associated to the key.
	 *
	 * @return string Returns the value of the item
	 */
	public function getValue()
	{
		return (string) $this->get( 'value', '' );
	}


	/**
	 * Sets the new value of the item.
	 *
	 * @param string $value Value of the item or null for no expiration
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setValue( $value )
	{
		return $this->set( 'value', (string) $value );
	}


	/**
	 * Returns the expiration time of the item.
	 *
	 * @return string|null Expiration time of the item or null for no expiration
	 */
	public function getTimeExpire()
	{
		return $this->get( 'expire' );
	}


	/**
	 * Sets the new expiration time of the item.
	 *
	 * @param string|null $timestamp Expiration time of the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setTimeExpire( $timestamp )
	{
		return $this->set( 'expire', $this->checkDateFormat( $timestamp ) );
	}


	/**
	 * Returns the tags associated to the item.
	 *
	 * @return array Tags associated to the item
	 */
	public function getTags()
	{
		return (array) $this->get( 'tags', [] );
	}


	/**
	 * Sets the new tags associated to the item.
	 *
	 * @param array $tags Tags associated to the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setTags( array $tags )
	{
		return $this->set( 'tags', $tags );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'cache';
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'cache.id': !$private ?: $item = $item->setId( $value ); break;
				case 'cache.value': $item = $item->setValue( $value ); break;
				case 'cache.expire': $item = $item->setTimeExpire( $value ); break;
				case 'cache.tags': $item = $item->setTags( $value ); break;
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
		$list = [];

		$list['cache.id'] = $this->getId();
		$list['cache.value'] = $this->getValue();
		$list['cache.expire'] = $this->getTimeExpire();
		$list['cache.tags'] = $this->getTags();

		if( $private === true ) {
			$list['cache.siteid'] = $this->getSiteId();
		}

		return $list;
	}
}
