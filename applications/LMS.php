<?php
class library_app extends application
{
	function __construct()
	{
		
		parent::__construct("library", _($this->help_context = "&LMS"));
		//$this->application("library", _($this->help_context = "&LMS"));
		$this->add_module(_("Maintainance Master"));
		$this->add_lapp_function(0, _("Return Policy "),"library/return_policy.php", 'SA_RETURNPOLICY', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Max Time for Extension"),"library/time_extension.php", 'SA_TIMEEXT', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Max. Books Allowed"),"library/book_allowed.php", 'SA_BOOKALLOW', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Max. Books on Hold"),"library/hold_allowed.php", 'SA_BOOKHOLD', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Book Fine "),"library/fine_policy.php", 'SA_BOOKFINE', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Add Book Category"),"library/add_category.php", 'SA_BOOKCATEGORY', MENU_TRANSACTION);
		
		$this->add_lapp_function(0, _("Add Author "),"library/add_auth.php", 'SA_ADDBOOK', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Add Publisher "),"library/add_publisher.php", 'SA_ADDBOOK', MENU_TRANSACTION);
                $this->add_lapp_function(0, _("Add Book "),"library/add_book.php", 'SA_ADDBOOK', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Add Floor "),"library/book_location.php", 'SA_ADDFLOOR', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Add Floor Aisle "),"library/floor_aisle.php", 'SA_ADDASILE', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Add Shelf "),"library/self.php", 'SA_ADDASILE', MENU_TRANSACTION);
		//$this->add_lapp_function(0, _("Book Location "),"library/book-sell.php", 'SA_BOOKMAP', //MENU_TRANSACTION);
		//$this->add_lapp_function(0, _("Book Shelf Management "),"library/book-sell-edit.php", 'SA_BOOKMAP', MENU_TRANSACTION);
		
		
		
		$this->add_module(_("Transactions"));
		$this->add_lapp_function(1, _("Book Reserve "),"library/bookReserve.php", 'SA_BOOKRESERVATION', MENU_TRANSACTION);
		$this->add_lapp_function(1, _("Book Issues (Student) "),"library/bookIssue.php", 'SA_BOOKISSUE', MENU_TRANSACTION);
		$this->add_lapp_function(1, _("Book Issues (Staff) "),"library/bookIssuestaff.php", 'SA_BOOKISSUESTAFF', MENU_TRANSACTION);
//		$this->add_lapp_function(1, _("CheckOut Book "),"library/checkoutHoldBook.php", 'SA_BOOKCHECKOUT', MENU_TRANSACTION);
                $this->add_lapp_function(1, _("Book Extension "),"library/extendedBook.php", 'SA_BOOKEXTENDED', MENU_TRANSACTION);
		$this->add_lapp_function(1, _("Return Book "),"library/returnbook.php", 'SA_BOOKRETURN', MENU_TRANSACTION);
		$this->add_lapp_function(1, _("Book Status "),"library/book-status.php", 'SA_BOOKSTATUS', MENU_TRANSACTION);

		$this->add_extensions();
	}
}


?>