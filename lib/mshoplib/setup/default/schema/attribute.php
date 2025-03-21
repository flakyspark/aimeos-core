<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 */


return array(
	'table' => array(
		'mshop_attribute_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', ['default' => 0] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msattty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msattty_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msattty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msattty_sid_code' );

			return $schema;
		},

		'mshop_attribute' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'key', 'string', array( 'length' => 32, 'default' => '' ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msatt_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'type', 'code' ), 'unq_msatt_sid_dom_type_code' );
			$table->addIndex( array( 'siteid', 'domain', 'status', 'type', 'pos' ), 'idx_msatt_sid_dom_stat_typ_pos' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msatt_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msatt_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msatt_sid_code' );
			$table->addIndex( array( 'siteid', 'type' ), 'idx_msatt_sid_type' );
			$table->addIndex( array( 'siteid', 'key' ), 'idx_msatt_sid_key' );

			return $schema;
		},

		'mshop_attribute_list_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', ['default' => 0] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattlity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msattlity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msattlity_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msattlity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msattlity_sid_code' );

			return $schema;
		},

		'mshop_attribute_list' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_list' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'key', 'string', array( 'length' => 134, 'default' => '' ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'refid', 'string', array( 'length' => 36 ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattli_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'domain', 'type', 'refid' ), 'unq_msattli_pid_sid_dm_ty_rid' );
			$table->addIndex( array( 'siteid', 'key' ), 'idx_msattli_sid_key' );
			$table->addIndex( array( 'parentid' ), 'fk_msattli_pid' );

			$table->addForeignKeyConstraint( 'mshop_attribute', array( 'parentid' ), array( 'id' ),
					array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msattli_pid' );

			return $schema;
		},

		'mshop_attribute_property_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_property_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', ['default' => 0] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattprty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msattprty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msattprty_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msattprty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msattprty_sid_code' );

			return $schema;
		},

		'mshop_attribute_property' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_property' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'key', 'string', array( 'length' => 103, 'default' => '' ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'value', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattpr_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'type', 'langid', 'value' ), 'unq_msattpr_sid_ty_lid_value' );
			$table->addIndex( array( 'siteid', 'key' ), 'fk_msattpr_sid_key' );
			$table->addIndex( array( 'parentid' ), 'fk_msattpr_pid' );

			$table->addForeignKeyConstraint( 'mshop_attribute', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msattpr_pid' );

			return $schema;
		},
	),
);
