<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the PostgreSQL database
 *
 * @package MW
 * @subpackage Setup
 */
class Pgsql extends \Aimeos\MW\Setup\DBSchema\InformationSchema
{
	/**
	 * Checks if the given table exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @return bool True if the table exists, false if not
	 */
	public function tableExists( string $tablename ) : bool
	{
		$sql = "
			SELECT TABLE_NAME
			FROM INFORMATION_SCHEMA.TABLES
			WHERE TABLE_TYPE = 'BASE TABLE'
				AND TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result !== false ? true : false;
	}


	/**
	 * Checks if the given sequence exists in the database.
	 *
	 * @param string $seqname Name of the database sequence
	 * @return bool True if the sequence exists, false if not
	 */
	public function sequenceExists( string $seqname ) : bool
	{
		$sql = "
			SELECT SEQUENCE_NAME
			FROM INFORMATION_SCHEMA.SEQUENCES
			WHERE SEQUENCE_SCHEMA = 'public'
				AND SEQUENCE_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $seqname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result !== false ? true : false;
	}


	/**
	 * Checks if the given constraint exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $constraintname Name of the database table constraint
	 * @return bool True if the constraint exists, false if not
	 */
	public function constraintExists( string $tablename, string $constraintname ) : bool
	{
		$sql = "
			SELECT CONSTRAINT_NAME
			FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
			WHERE TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
				AND CONSTRAINT_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $constraintname );
		$result = $stmt->execute()->fetch();

		if( $result === false )
		{
			$sql = "
				SELECT indexname
				FROM pg_indexes
				WHERE schemaname = 'public'
					AND tablename = ?
					AND indexname = ?
			";

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $tablename );
			$stmt->bind( 2, $constraintname );
			$result = $stmt->execute()->fetch();
		}

		$this->release( $conn );

		return $result !== false ? true : false;
	}


	/**
	 * Checks if the given column exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return bool True if the column exists, false if not
	 */
	public function columnExists( string $tablename, string $columnname ) : bool
	{
		$sql = "
			SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $columnname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result !== false ? true : false;
	}


	/**
	 * Checks if the given index (not foreign keys, primary or unique constraints) exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $indexname Name of the database index
	 * @return bool True if the index exists, false if not
	 */
	public function indexExists( string $tablename, string $indexname ) : bool
	{
		$sql = "
			SELECT indexname
			FROM pg_indexes
			WHERE schemaname = 'public'
				AND tablename = ?
				AND indexname = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $indexname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result !== false ? true : false;
	}


	/**
	 * Returns an object containing the details of the column.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Object which contains the details
	 */
	public function getColumnDetails( string $tablename, string $columnname ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		$sql = "
			SELECT *
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $columnname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		if( $result === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unknown column "%1$s" in table "%2$s"', $columnname, $tablename ) );
		}

		return $this->createColumnItem( $result );
	}


	/**
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		switch( $record['data_type'] )
		{
			case 'character varying': $type = 'varchar'; break;
			default: $type = $record['data_type'];
		}

		$length = ( isset( $record['character_maximum_length'] ) ? $record['character_maximum_length'] : $record['numeric_precision'] );
		$default = ( preg_match( '/^\'(.*)\'::.+$/', $record['column_default'], $match ) === 1 ? $match[1] : $record['column_default'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['table_name'], $record['column_name'],
			$type, $length, $default, $record['is_nullable'], $record['character_set_name'], $record['collation_name'] );
	}
}
