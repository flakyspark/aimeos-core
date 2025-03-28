<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Product\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();

		$this->object = new \Aimeos\MShop\Product\Manager\Standard( $this->context );
	}

	protected function tearDown()
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$item = ( new \Aimeos\MShop\Product\Item\Standard() )->setId( -1 );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->deleteItems( [-1] ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->deleteItems( [$item] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->createItem() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->createItem( ['product.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( \Aimeos\MW\Criteria\SQL::class, $search );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'product', $result );
		$this->assertContains( 'product/lists', $result );
		$this->assertContains( 'product/property', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'CNC' );

		$this->assertEquals( 'CNC', $item->getCode() );
	}


	public function testFindItemDeep()
	{
		$item = $this->object->findItem( 'CNE', ['attribute', 'product'] );
		$products = $item->getRefItems( 'product' );
		$product = reset( $products );

		$this->assertEquals( 4, count( $products ) );
		$this->assertNotEquals( false, $product );
		$this->assertEquals( 'CNC', $product->getCode() );
		$this->assertEquals( 1, count( $product->getRefItems( 'attribute' ) ) );
	}


	public function testFindItemDomainFilter()
	{
		$item = $this->object->findItem( 'CNE', ['product' => ['default']] );
		$this->assertEquals( 3, count( $item->getListItems( 'product' ) ) );
	}


	public function testFindItemForeignDomains()
	{
		$item = $this->object->findItem( 'CNE', ['catalog', 'supplier'] );

		$this->assertEquals( 1, count( $item->getSupplierItems() ) );
		$this->assertEquals( 2, count( $item->getCatalogItems() ) );
	}


	public function testGetItem()
	{
		$domains = ['text', 'product', 'price', 'media' => ['unittype10'], 'attribute', 'product/property' => ['package-weight']];

		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
				$search->compare( '==', 'product.code', 'CNC' ),
				$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->object->searchItems( $search, $domains );

		if( ( $product = reset( $products ) ) === false ) {
			throw new \RuntimeException( sprintf( 'Found no Productitem with text "%1$s"', 'Cafe Noire Cappuccino' ) );
		}

		$this->assertEquals( $product, $this->object->getItem( $product->getId(), $domains ) );
		$this->assertEquals( 6, count( $product->getRefItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $product->getRefItems( 'media', null, null, false ) ) );
		$this->assertEquals( 1, count( $product->getPropertyItems() ) );
	}


	public function testSaveItems()
	{
		$search = $this->object->createSearch();
		$conditions = array(
				$search->compare( '==', 'product.code', 'CNC' ),
				$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		$this->object->saveItems( $items );
	}


	public function testSaveUpdateDeleteItem()
	{
		$listItem = $this->object->createListsItem();
		$refItem = \Aimeos\MShop\Text\Manager\Factory::create( $this->context )->createItem()->setType( 'name' );

		$search = $this->object->createSearch();
		$conditions = array(
				$search->compare( '==', 'product.code', 'CNC' ),
				$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No product item found' );
		}

		$item->setId( null );
		$item->setCode( 'CNC unit test' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unit save test' )->addListItem( 'text', $listItem, $refItem );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId(), ['text'] );

		$this->object->deleteItem( $itemUpd->deleteListItems( $itemUpd->getListItems( 'text' ), true ) );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDataset(), $itemSaved->getDataset() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getTarget(), $itemSaved->getTarget() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDataset(), $itemUpd->getDataset() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getTarget(), $itemUpd->getTarget() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetSavePropertyItems()
	{
		$item = $this->object->findItem( 'CNE', ['product/property'] );

		$item->setId( null )->setCode( 'xyz' );
		$this->object->saveItem( $item );

		$item2 = $this->object->findItem( 'CNE', ['product/property'] );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 4, count( $item->getPropertyItems() ) );
		$this->assertEquals( 4, count( $item2->getPropertyItems() ) );
	}


	public function testSaveItemRefItems()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $context );

		$item = $manager->createItem();
		$item->setType( 'default' );
		$item->setCode( 'unitreftest' );

		$listManager = $manager->getSubManager( 'lists' );

		$listItem = $listManager->createItem();
		$listItem->setType( 'default' );

		$textManager = \Aimeos\MShop\Text\Manager\Factory::create( $context );

		$textItem = $textManager->createItem();
		$textItem->setType( 'name' );


		$item->addListItem( 'text', $listItem, $textItem );

		$item = $manager->saveItem( $item );
		$item2 = $manager->getItem( $item->getId(), ['text'] );

		$item->deleteListItem( 'text', $listItem, $textItem );

		$item = $manager->saveItem( $item );
		$item3 = $manager->getItem( $item->getId(), ['text'] );

		$manager->deleteItem( $item->getId() );


		$this->assertEquals( 0, count( $item->getRefItems( 'text', 'name', 'default', false ) ) );
		$this->assertEquals( 1, count( $item2->getRefItems( 'text', 'name', 'default', false ) ) );
		$this->assertEquals( 0, count( $item3->getRefItems( 'text', 'name', 'default', false ) ) );
	}


	public function testSaveItemSitecheck()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( \TestHelperMShop::getContext() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
		$products = $manager->searchItems( $search );

		if( ( $item = reset( $products ) ) === false ) {
			throw new \RuntimeException( 'No product found' );
		}

		$item->setId( null );
		$item->setCode( 'unittest' );

		$manager->saveItem( $item );
		$manager->getItem( $item->getId() );
		$manager->deleteItem( $item->getId() );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$manager->getItem( $item->getId() );
	}


	public function testSearchItems()
	{
		$item = $this->object->findItem( 'CNE', ['product'] );

		if( ( $listItem = current( $item->getListItems( 'product', 'suggestion' ) ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'product.id', null );
		$expr[] = $search->compare( '!=', 'product.siteid', null );
		$expr[] = $search->compare( '==', 'product.type', 'default' );
		$expr[] = $search->compare( '==', 'product.code', 'CNE' );
		$expr[] = $search->compare( '==', 'product.dataset', 'Coffee' );
		$expr[] = $search->compare( '==', 'product.label', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '~=', 'product.config', 'css-class' );
		$expr[] = $search->compare( '==', 'product.datestart', null );
		$expr[] = $search->compare( '==', 'product.dateend', null );
		$expr[] = $search->compare( '==', 'product.status', 1 );
		$expr[] = $search->compare( '>=', 'product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.editor', $this->editor );
		$expr[] = $search->compare( '>=', 'product.target', '' );

		$param = ['product', ['suggestion', 'invalid'], $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->createFunction( 'product:has', $param ), null );

		$param = ['product', 'suggestion'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'product:has', $param ), null );

		$param = ['product'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'product:has', $param ), null );

		$param = ['package-weight', null, '1'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'product:prop', $param ), null );

		$param = ['package-weight', null];
		$expr[] = $search->compare( '!=', $search->createFunction( 'product:prop', $param ), null );

		$param = ['package-weight'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'product:prop', $param ), null );


		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsAll()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$search->setSlice( 0, 10 );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 28, $total );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array( 'media' ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchWildcards()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'CN_' ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $result ) );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'CN%' ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $result ) );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'CN[C]' ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testSearchItemsLimit()
	{
		$start = 0;
		$numproducts = 0;

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', 'core:lib/mshoplib' ) );
		$search->setSlice( $start, 5 );

		do
		{
			$result = $this->object->searchItems( $search );

			foreach( $result as $item ) {
				$numproducts++;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 5 );
		}
		while( $count > 0 );

		$this->assertEquals( 28, $numproducts );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'property' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'property', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
