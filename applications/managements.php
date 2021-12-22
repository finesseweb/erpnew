<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL,
	as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
class managements_app extends application
{
	function __construct()
	{
		parent::__construct("managements", _($this->help_context = "&Managements"));
			
		$this->add_module(_("Transactions"));
                $this->add_lapp_function(0, _("Create &Assets"),
			"managements/manage/createassets.php", 'SA_CREATE_ASSETS', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Assemble &Assets"),
			"managements/manage/assets.php?NewProduct=Yes", 'SA_ASSEMBLE', MENU_TRANSACTION);

//		$this->add_module(_("Inquiries and Reports"));
//		$this->add_lapp_function(1, _("Fixed Assets &Movements"),
//			"inventory/inquiry/stock_movements.php?FixedAsset=1", 'SA_ASSETSTRANSVIEW', MENU_INQUIRY);
//
//
//		$this->add_module(_("Maintenance"));
//		
//		$this->add_lapp_function(2, _("Fixed &Assets"),
//			"inventory/manage/items.php?FixedAsset=1", 'SA_ASSET', MENU_ENTRY);

		$this->add_extensions();
	}
}


?>
