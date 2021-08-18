<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL,
	as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
class tally_app extends application
{
	function __construct()
	{
		//$this->application("tally", _($this->help_context = "&Tally System"));
                 parent::__construct("tally", _($this->help_context = "&Tally System"));
		$this->add_module(_("Transactions"));
			$this->add_lapp_function(0, _("&Tally Sync"),
			"tally/manage/tally_sync.php?", 'SA_TALLY_SYNC', MENU_ENTRY);

		//$this->add_module(_("Tally Reports"));
		//$this->add_lapp_function(1, _("&Statutory Report"),
		//	"statutory/manage/statutory_report.php?", 'SA_STATUTORY_REPORT', MENU_ENTRY);

		//$this->add_module(_("Tally Masters"));
	//	$this->add_lapp_function(2, _("&Create"),
	//		"tally/manage/tally_create.php?", 'SA_TALLY_CREATE', MENU_ENTRY);
          //      $this->add_lapp_function(2, _("&Modify"),
	//		"tally/manage/tally_modify.php?", 'SA_TALLY_MODIFY', MENU_ENTRY);
	//		$this->add_lapp_function(2, _("&Delete"),
	//		"tally/manage/tally_delete.php?", 'SA_TALLY_DELETE', MENU_ENTRY);
	//	$this->add_extensions();
        }
}


?>