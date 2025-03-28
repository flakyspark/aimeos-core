<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the weekday values in order tables
 */
class OrderAddWeekday extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Migrate database schema
	 */
	public function migrate()
	{
		$dbdomain = 'db-order';
		$this->msg( 'Populate weekday column in order table', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_order' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$start = 0;
		$conn = $this->acquire( $dbdomain );
		$select = 'SELECT "id", "ctime" FROM "mshop_order" WHERE "cwday" = \'\' LIMIT 1000 OFFSET :offset';
		$update = 'UPDATE "mshop_order" SET "cwday" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );

		do
		{
			$count = 0;
			$map = [];
			$sql = str_replace( ':offset', $start, $select );
			$result = $conn->create( $sql )->execute();

			while( ( $row = $result->fetch() ) !== false )
			{
				$map[$row['id']] = $row['ctime'];
				$count++;
			}

			foreach( $map as $id => $ctime )
			{
				list( $date, $time ) = explode( ' ', $ctime );

				$stmt->bind( 1, date_create_from_format( 'Y-m-d', $date )->format( 'w' ) );
				$stmt->bind( 2, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
			}

			$start += $count;
		}
		while( $count === 1000 );

		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
