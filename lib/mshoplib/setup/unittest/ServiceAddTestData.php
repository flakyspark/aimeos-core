<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds service test data.
 */
class ServiceAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds service test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding service test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'service.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
		}

		$this->addServiceData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the service test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addServiceData( array $testdata )
	{
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->additional, 'Standard' );
		$serviceTypeManager = $serviceManager->getSubManager( 'type', 'Standard' );

		$type = $serviceTypeManager->createItem();

		$serviceManager->begin();

		foreach( $testdata['service/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$serviceTypeManager->saveItem( $type );
		}

		$parent = $serviceManager->createItem();

		foreach( $testdata['service'] as $key => $dataset )
		{
			$parent->setId( null );
			$parent->setType( $dataset['type'] );
			$parent->setPosition( $dataset['pos'] );
			$parent->setCode( $dataset['code'] );
			$parent->setLabel( $dataset['label'] );
			$parent->setProvider( $dataset['provider'] );
			$parent->setConfig( $dataset['config'] );
			$parent->setStatus( $dataset['status'] );

			$serviceManager->saveItem( $parent, false );
		}

		$serviceManager->commit();
	}
}
