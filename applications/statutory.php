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
class statutory_app extends application
{
	function __construct()
	{
		//$this->application("statutory", _($this->help_context = "&Statutory Compliances"));
                 parent::__construct("statutory", _($this->help_context = "&Statutory Compliances"));
		$this->add_module(_("Transactions"));
			$this->add_lapp_function(0, _("&Statutory Transactions"),
			"statutory/manage/statutory_transction.php?", 'SA_STATUTORY_USER', MENU_ENTRY);

		$this->add_module(_("Statutory Reports"));
		$this->add_lapp_function(1, _("&Statutory Report"),
			"statutory/manage/statutory_report.php?", 'SA_STATUTORY_REPORT', MENU_ENTRY);

		$this->add_module(_("Statutory Masters"));
		$this->add_lapp_function(2, _("&Statutory Master"),
			"statutory/manage/statutory_master.php?", 'SA_STATUTORY_MASTER', MENU_ENTRY);
                $this->add_lapp_function(2, _("&Statutory Body Master"),
			"statutory/manage/statutory_body.php?", 'SA_STATUTORY_BODY', MENU_ENTRY);
			$this->add_lapp_function(2, _("&Name of Compliance"),
			"statutory/manage/name_return.php?", 'SA_STATUTORY_NAME_RETURN', MENU_ENTRY);
		$this->add_extensions();
	}
}


?>