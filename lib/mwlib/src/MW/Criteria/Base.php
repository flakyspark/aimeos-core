<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria;


/**
 * Abstract search class
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base implements \Aimeos\MW\Criteria\Iface
{
	/**
	 * Creates a function signature for expressions.
	 *
	 * @param string $name Function name
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 */
	public function createFunction( $name, array $params )
	{
		return \Aimeos\MW\Criteria\Expression\Base::createFunction( $name, $params );
	}


	/**
	 * Creates condition expressions from a multi-dimensional associative array.
	 *
	 * The simplest form of a valid associative array is a single comparison:
	 * 	$array = array(
	 * 		'==' => array( 'name' => 'value' ),
	 * 	);
	 *
	 * Combining several conditions can look like:
	 * 	$array = array(
	 * 		'&&' => array(
	 * 			0 => array(
	 * 				'==' => array( 'name' => 'value' ),
	 * 			1 => array(
	 * 				'==' => array( 'name2' => 'value2' ),
	 * 			),
	 * 		),
	 * 	);
	 *
	 * Nested combine operators are also possible.
	 *
	 * @param array $array Multi-dimensional associative array containing the expression arrays
	 * @return \Aimeos\MW\Criteria\Expression\Iface|null Condition expressions (maybe nested) or null for none
	 * @throws \Aimeos\MW\Common\Exception If given array is invalid
	 */
	public function toConditions( array $array )
	{
		if( ( $value = reset( $array ) ) === false ) {
			return null;
		}

		$op = key( $array );
		$operators = $this->getOperators();

		if( in_array( $op, $operators['combine'], true ) ) {
			return $this->createCombineExpression( $op, (array) $value );
		}
		else if( in_array( $op, $operators['compare'], true ) ) {
			return $this->createCompareExpression( $op, (array) $value );
		}

		throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $op ) );
	}


	/**
	 * Creates sortation expressions from an associative array.
	 *
	 * The array must be a single-dimensional array of name and operator pairs like
	 * 	$array = array(
	 * 		'name' => '+',
	 * 		'name2' => '-',
	 * 	);
	 *
	 * @param string[] $array Single-dimensional array of name and operator pairs
	 * @return \Aimeos\MW\Criteria\Expression\Sort\Iface[] List of sort expressions
	 */
	public function toSortations( array $array )
	{
		$results = [];

		foreach( $array as $name => $op ) {
			$results[] = $this->sort( $op, $name );
		}

		return $results;
	}


	/**
	 * Returns the list of translated colums
	 *
	 * @param array $columns List of objects implementing getName() method
	 * @param array $translations Associative list of item names that should be translated
	 * @return array List of translated columns
	 */
	public function translate( array $columns, array $translations = [] )
	{
		$list = [];

		foreach( $columns as $item )
		{
			if( ( $value = $item->translate( $translations ) ) !== null ) {
				$list[] = $value;
			}
		}

		return $list;
	}


	/**
	 * Creates a "combine" expression.
	 *
	 * @param string $operator One of the "combine" operators
	 * @param array $list List of arrays with "combine" or "compare" representations
	 * @throws \Aimeos\MW\Common\Exception If operator is invalid
	 */
	protected function createCombineExpression( $operator, array $list )
	{
		$results = [];
		$operators = $this->getOperators();

		foreach( $list as $entry )
		{
			$entry = (array) $entry;

			if( ( $op = key( $entry ) ) === null ) {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid combine condition array "%1$s"', json_encode( $entry ) ) );
			}

			if( in_array( $op, $operators['combine'] ) ) {
				$results[] = $this->createCombineExpression( $op, (array) $entry[$op] );
			}
			else if( in_array( $op, $operators['compare'] ) ) {
				$results[] = $this->createCompareExpression( $op, (array) $entry[$op] );
			}
			else {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $op ) );
			}
		}

		return $this->combine( $operator, $results );
	}


	/**
	 * Creates a "compare" expression.
	 *
	 * @param string $op One of the "compare" operators
	 * @param array $pair Associative list containing one name/value pair
	 * @throws \Aimeos\MW\Common\Exception If no name/value pair is available
	 */
	protected function createCompareExpression( $op, array $pair )
	{
		if( ( $value = reset( $pair ) ) === false ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid compare condition array "%1$s"', json_encode( $pair ) ) );
		}

		return $this->compare( $op, key( $pair ), $value );
	}
}
