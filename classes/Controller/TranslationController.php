<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Controller;

/**
 * Translation Controller
 *
 * Temporarily handles translations for some JS modules
 *
 * @author Tamás Barta <barta.tamas.d@gmail.com>
 */
class TranslationController extends \Controller_Rest
{
	public function action_datatables()
	{
		return array (
			'sEmptyTable'        => gettext('No data available in table'),
			'sInfo'              => gettext('Showing _START_ to _END_ of _TOTAL_ entries'),
			'sInfoEmpty'         => gettext('Showing 0 to 0 of 0 entries'),
			'sInfoFiltered'      => gettext('(filtered from _MAX_ total entries)'),
			'sInfoPostFix'       => '',
			// 'sInfoThousands'  => gettext(','),
			'sLengthMenu'        => gettext('Show _MENU_ entries'),
			'sLoadingRecords'    => gettext('Loading...'),
			'sProcessing'        => gettext('Processing...'),
			'sSearch'            => '',
			'sSearchPlaceholder' => gettext('Search all fields'),
			'sZeroRecords'       => gettext('No matching records found'),
			'oPaginate'          => array (
				'sFirst'    => gettext('First'),
				'sPrevious' => gettext('Previous'),
				'sNext'     => gettext('Next'),
				'sLast'     => gettext('Last'),
			),
			'oAria'              => array (
				'sSortAscending'  => gettext(': activate to sort column ascending'),
				'sSortDescending' => gettext(': activate to sort column descending'),
			),
		);
	}
}
