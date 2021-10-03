<?php
namespace Booking\Controllers;
class FAQ {
	private $faqsTable;

	public function __construct($faqsTable) {
		$this->faqsTable = $faqsTable;
	}

    public function faqGet() {
        $faq = $this->faqsTable->findAll();

        return [
            'template' => 'faq.html.php',
            'variables' => ['faq' => $faq],
            'title' => 'Power-Time - FAQs'
        ];
    }

    public function faqSubmit() {

        $faqs = $_POST['faq'];

        $this->faqsTable->save($faqs);

        $message = [0 => 'New question and answer submitted and will be displayed once you reload page.'];
        header('refresh: 6; url=/faqs');
        return [
            'template' => 'applyMessage.html.php',
            'variables' => ['message' => $message[0]],
            'title' => 'Power-Time - FAQs'
        ];
    }

    public function faqEditForm() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
            if ($_SESSION['access_level'] ==='admin' || $_SESSION['access_level'] ==='manager') {
                if(isset($_GET['id'])) {

                    $faqs = $this->faqsTable->find('faqID', $_GET['id'])[0];

                    return [
                        'template' => 'admin/faqsEdit.html.php',
                        'variables' => ['faqs' => $faqs],
                        'title' => 'Power-Time - Edit FAQs'
                    ];
                }
                else {
                    header('location: /');
                }
            }
            else {
                header('location: /faqs');
            }
        }
        else {
            header('location: /');    
        }
    }

    public function faqEditSubmit() {

        $faqs = $_POST['faq'];

        $this->faqsTable->save($faqs);

        $message = [0 => 'Questions and Answers Updated.'];
        header('refresh: 5; url=/faqs');
        return [
            'template' => 'applyMessage.html.php',
            'variables' => ['message' => $message[0]],
            'title' => 'Power-Time - FAQs'
        ];
    }

    public function faqDeleteCheck() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
            if ($_SESSION['access_level'] ==='admin' || $_SESSION['access_level'] ==='manager') {
                if(isset($_GET['id'])) {

                    $faqs = $this->faqsTable->find('faqID', $_GET['id'])[0];

                    return [
                        'template' => 'admin/faqDeleteCheck.html.php',
                        'variables' => ['faqs' => $faqs],
                        'title' => 'Power-Time - Delete FAQs'
                    ];
                }
                else {
                    header('location: /');
                }
            }
            else {
                header('location: /faqs');
            }
        }
        else {
            header('location: /');    
        }
    }

    public function faqDeleteConfirmed() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
            if ($_SESSION['access_level'] ==='admin' || $_SESSION['access_level'] ==='manager') {
                $this->faqsTable->delete($_POST['id']);

                $message = [0 => 'FAQ Deleted.'];
                header('refresh: 4; url=/faqs');
                return [
                    'template' => 'applyMessage.html.php',
                    'variables' => ['message' => $message[0]],
                    'title' => 'Power-Time - Shift'
                ];
            }
            else {
                header('location: /');
            }
        }
        else {
            header('location: /');
        }
    }
}