<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 201
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds default records to plugin table.
 */
class MShopAddPluginDataDefault extends \Aimeos\MW\Setup\Task\MShopAddPluginData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopAddTypeDataDefault'];
	}
}
