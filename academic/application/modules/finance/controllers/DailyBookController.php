<?php


class Finance_DailyBookController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'daily-book';

        $ErpFinanceDailyBook_model = new Finance_Model_ErpFinanceDailyBook();
        $daily_book_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        switch ($type) {
            case "add":
                $ErpFinanceDailyBook_form = new Finance_Form_ErpFinanceDailyBook();
                $this->view->type = $type;
                $this->view->ErpFinanceDailyBook_form = $ErpFinanceDailyBook_form;
                if ($this->getRequest()->isPost()) {
                    if ($ErpFinanceDailyBook_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpFinanceDailyBook_form->getValues();
                        $data['added_date'] = date("Y-m-d h:i:s");
                        $ErpFinanceDailyBook_model->insert($data);
                        $this->_flashMessenger->addMessage('Finance Successfully added');
                        $this->_redirect('finance/daily-book/index');
                    }
                }
                break;
            case 'edit':
                $ErpFinanceDailyBook_form = new Finance_Form_ErpFinanceDailyBook();
                $this->view->type = $type;
                $this->view->ErpFinanceDailyBook_form = $ErpFinanceDailyBook_form;
                $result = $ErpFinanceDailyBook_model->getRecord($daily_book_id);
                $ErpFinanceDailyBook_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    if ($ErpFinanceDailyBook_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpFinanceDailyBook_form->getValues();
                        $ErpFinanceDailyBook_model->update($data, array('daily_book_id=?' => $daily_book_id));
                        $this->_flashMessenger->addMessage('Finance Updated Successfully');
                        $this->_redirect('finance/daily-book/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($daily_book_id)
                    $ErpFinanceDailyBook_model->update($data, array('daily_book_id=?' => $daily_book_id));
                $this->_flashMessenger->addMessage('Finance Deleted Successfully');
                $this->_redirect('finance/daily-book/index');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ErpFinanceDailyBook_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	public function dailyBookViewAction()
	{
		
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {
			$this->view->module = "finance";
			$this->view->action_name = 'index';
			$this->view->controller = 'journal-ledger';
			$daily_book_id = $this->_getParam("name");
			$ErpFinanceDailyBook_model = new Finance_Model_ErpFinanceDailyBook();
			$result = $ErpFinanceDailyBook_model->getRecord($daily_book_id);
			//echo '<pre>'; print_r($result); die;
			$this->view->result = $result;
			$this->_helper->viewRenderer("daily-book-view");         	
        }		
		
	}

	
}
