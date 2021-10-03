<?php
namespace Booking\Controllers;
class Enquiry {
    private $enquiryTable;
	private $employeeTable;
    private $webDB;

	public function __construct($enquiryTable, $employeeTable, $webDB) {
		$this->enquiryTable = $enquiryTable;
        $this->employeeTable = $employeeTable;
        $this->webDB = $webDB;
	}

    //
    public function contact() {
        return [
            'template' => 'contact.html.php',
            'variables' => [],
            'title' => 'Power-Time - Enquiry'
        ];
    }

    public function enquiryForm() {
        $date = new \DateTime();
        $enquiry_date = $date->format('Y-m-d');
        return [
            'template' => 'enquiry.html.php',
            'variables' => ['enquiry_date' => $enquiry_date],
            'title' => 'Power-Time - Enquiry'
        ];
    }

    public function enquirySubmit() {
        $enquiry = $_POST['enquiry'];
		if ($enquiry['enquiry_message'] !== '') {

			$this->enquiryTable->save($enquiry);

            $message = [0 => 'Enquiry submitted, a member of the staff will get back to you as soon as possible. You will be able to see the reply under "Contact" located on the navigation bar.'];
			header('refresh: 15; url=/');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Enquiry'
			];
        }
        else {
			$message = [0 => 'Empty fields, please try again.'];
			header('refresh: 4; url=/shift');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Enquiry'
			];
		}
    }

    public function enquiryDisplay() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE) {
            $enquiry = $this->enquiryTable->find('employeeID', $_SESSION['loggedin']);
            return [
				'template' => 'equiryDisplay.html.php',
				'variables' => ['enquiry' => $enquiry],
				'title' => 'Power-Time - Enquiry'
			];
        }

        else {
            header('location: /');
        }
    }

    public function enquiryAdmin() {
        return [
            'template' => 'admin/enquiryAdmin.html.php',
            'variables' => [],
            'title' => 'Power-Time - Admin Enquiry'
        ];
    }

    public function enquiryAnswerForm() {
        $enquiries = $this->webDB->enquiryAnswer();
        return [
            'template' => 'admin/enquiryAnswer.html.php',
            'variables' => ['enquiries' => $enquiries],
            'title' => 'Power-Time - Enquiry'
        ];
    }

    public function enquirySolveForm() {
        if (isset($_GET['id'])) {
            $enquiry = $this->webDB->enquirySolve($_GET['id'])[0];
            return [
                'template' => 'admin/enquirySolve.html.php',
                'variables' => ['enquiry' => $enquiry],
                'title' => 'Power-Time - Solve Enquiry'
            ];
        }

        else {
            header('location: /enquiry-answer');
        }
    }

    public function enquirySolveSubmit() {
        $enquiry = $_POST['enquiry'];
		if ($enquiry['replier_message'] !== '') {

			$this->enquiryTable->save($enquiry);

            $message = [0 => 'Enquiry reply submitted and enquiry archived.'];
			header('refresh: 5; url=/enquiry-admin');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Enquiry'
			];
        }
        else {
			$message = [0 => 'Empty fields, please try again.'];
			header('refresh: 4; url=/enquiry-answer');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Enquiry'
			];
		}
    }

    public function enquiryArchive() {
        $enquiry = $this->webDB->enquiryFindArchived('archived');
        return [
            'template' => 'admin/enquiryArchive.html.php',
            'variables' => ['enquiry' => $enquiry],
            'title' => 'Power-Time - Archived Enquiries'
        ];
    }

    public function enquiryDisplayUser() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {

            $unresolved = $this->webDB->enquiryDisplayForms($_SESSION['loggedin'], 'unresolved');
            $archieved = $this->webDB->enquiryDisplayForms($_SESSION['loggedin'], 'archived');

            return [
                'template' => 'enquiryDisplay.html.php',
                'variables' => ['unresolved' => $unresolved, 'archieved' => $archieved],
                'title' => 'Power-Time - Enquiries'
            ];
        }
        else {
            header('location: /');
        }
    }

    public function deleteEnquiryCheck() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
            if ($_SESSION['access_level'] ==='admin' || $_SESSION['access_level'] ==='manager') {
                if (isset($_GET['id'])) {

                    $enquiry = $this->webDB->enquiryDeleteCheck($_GET['id'])[0];
                    return [
                        'template' => 'admin/enquiryDeleteCheck.html.php',
                        'variables' => ['enquiry' => $enquiry],
                        'title' => 'Power-Time - Enquiry Delete'
                    ];
                }
                else {
                    header('location: /enquiry-answer');
                }

            }

            else {
                header('location: /');
            }
        }

        else {
            header('location: /');
        }

    }
    public function deleteEnquiryConfirm() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
            if ($_SESSION['access_level'] ==='admin' || $_SESSION['access_level'] ==='manager') {
                $this->enquiryTable->delete($_POST['id']);

                $message = [0 => 'Enquiry Record Deleted.'];
                header('refresh: 4; url=/enquiry-answer');
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