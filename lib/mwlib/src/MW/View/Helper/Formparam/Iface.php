<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Formparam;


/**
 * View helper class for generating form parameter names.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the name of the form parameter.
	 * The result is a string that allows parameters to be passed as arrays if
	 * this is necessary, e.g. "name1[name2][name3]..."
	 *
	 * @param string|array $names Name or list of names
	 * @return string Form parameter name
	 */
	public function transform( $names ) : string;
}
