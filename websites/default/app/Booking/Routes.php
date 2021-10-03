<?php
namespace Booking;

class Routes implements \CSY\Routes {
	
	public function getRoutes() {
		require '../app/database.php';

		//Database instances
        $employeeTable = new \CSY\DatabaseTable($pdo, 'employee', 'employeeID');
		$shiftTable = new \CSY\DatabaseTable($pdo, 'shift', 'shiftID');
		$holidayTable = new \CSY\DatabaseTable($pdo, 'holiday', 'holidayID');
		$faqsTable = new \CSY\DatabaseTable($pdo, 'faq', 'faqID');
		$enquiryTable = new \CSY\DatabaseTable($pdo, 'enquiry', 'enquiryID');

		//DB with special functions for the Booking website
		$webDB = new \Booking\Database\WebDB($pdo, 'shift');

		//Controllers
        $employeeController = new \Booking\Controllers\Employee($employeeTable);
		$shiftController = new \Booking\Controllers\Shift($employeeTable, $shiftTable, $webDB, $holidayTable);
		$holidayController = new \Booking\Controllers\Holiday($holidayTable, $employeeTable, $webDB);
		$enquiryController = new \Booking\Controllers\Enquiry($enquiryTable, $employeeTable, $webDB);
		$faqController = new \Booking\Controllers\FAQ($faqsTable);

		//Website routes
		$routes = [
			'' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'home'
				]
			],
			'register' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'registerForm'
				],
				'POST' => [
					'controller' => $employeeController,
					'function' => 'registerSubmit'
				]
			],
			'login' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'loginForm'
				],
				'POST' => [
					'controller' => $employeeController,
					'function' => 'loginSubmit'
				]
			],
			'logout' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'logout'
				],
				'login' => true
			],
			'admin-home' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'adminHome'
				],
				'login' => true
			],
			'employees-list' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'employeeList'
				],
				'login' => true
			],
			'admin-edit-employee' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'employeeEditFormAdmin'
				],
				'POST' => [
					'controller' => $employeeController,
					'function' => 'employeeEditAdminSubmit'
				],
				'login' => true
			],
			'admin-display-employee-record' => [
				'GET' => [
					'controller' => $employeeController,
					'function' => 'employeeDisplay'
					]
				],
			'shift-listing' => [
				'GET' => [
					'controller' => $shiftController,
					'function' => 'shiftList'
					]
				],
			'shift' => [
				'GET' => [
					'controller' => $shiftController,
					'function' => 'shiftForm'
				],
				'POST' => [
					'controller' => $shiftController,
					'function' => 'shiftSubmit'
				],
				'login' => true
			],
			'shift-display' => [
				'GET' => [
					'controller' => $shiftController,
					'function' => 'shiftDisplayForm'
				],
				'POST' => [
					'controller' => $shiftController,
					'function' => 'shiftDisplay'
				],
				'login' => true
			],
			'admin-shift-delete' => [
				'GET' => [
					'controller' => $shiftController,
					'function' => 'deleteConfirm'
				],
				'login' => true
			],
			'admin-shift-delete-confirmed' => [
				'POST' => [
					'controller' => $shiftController,
					'function' => 'deleteShift'
				],
				'login' => true
			],
			'holiday' => [
				'GET' => [
					'controller' => $holidayController,
					'function' => 'holiday'
				]
			],
			'holiday-book' => [
				'GET' => [
					'controller' => $holidayController,
					'function' => 'holidayForm'
				],
				'POST' => [
					'controller' => $holidayController,
					'function' => 'holidaySubmit'
				],
				'login' => true
			],
			'holiday-display' => [
				'GET' => [
					'controller' => $holidayController,
					'function' => 'holidayDisplay'
				]
			],
			'holiday-requests' => [
				'GET' => [
					'controller' => $holidayController,
					'function' => 'holidayRequest'
				]
			],
			'holiday-application-answer' => [
				'GET' => [
					'controller' => $holidayController,
					'function' => 'holidayRequestAnswerForm'
				],
				'POST' => [
					'controller' => $holidayController,
					'function' => 'holidayRequestAnswerSubmit'
				],
				'login' => true
			],
			'contact' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'contact'
				],
				'login' => true
			],
			'contact-form' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquiryForm'
				],
				'POST' => [
					'controller' => $enquiryController,
					'function' => 'enquirySubmit'
				],
				'login' => true
			],
			'enquiry-admin' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquiryAdmin'
				],
				'login' => true
			],
			'contact-form' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquiryForm'
				],
				'POST' => [
					'controller' => $enquiryController,
					'function' => 'enquirySubmit'
				],
				'login' => true
			],
			'enquiry-answer' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquiryAnswerForm'
				],
				'login' => true
			],
			'delete-enquiry-check' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'deleteEnquiryCheck'
				],
				'login' => true
			],
			'delete-enquiry-confirmed' => [
				'POST' => [
					'controller' => $enquiryController,
					'function' => 'deleteEnquiryConfirm'
				],
				'login' => true
			],
			'answer-employee-enquiry' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquirySolveForm'
				],
				'POST' => [
					'controller' => $enquiryController,
					'function' => 'enquirySolveSubmit'
				],
				'login' => true
			],
			'enquiry-display-archived' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquiryArchive'
				],
				'login' => true
			],
			'contact-display' => [
				'GET' => [
					'controller' => $enquiryController,
					'function' => 'enquiryDisplayUser'
				],
				'login' => true
			],
			'faqs' => [
				'GET' => [
					'controller' => $faqController,
					'function' => 'faqGet'
				],
				'POST' => [
					'controller' => $faqController,
					'function' => 'faqSubmit'
				]
			],
			'faqs-edit' => [
				'GET' => [
					'controller' => $faqController,
					'function' => 'faqEditForm'
				],
				'POST' => [
					'controller' => $faqController,
					'function' => 'faqEditSubmit'
				],
				'login' => true
			],
			'faqs-delete-check' => [
				'GET' => [
					'controller' => $faqController,
					'function' => 'faqDeleteCheck'
				],
				'login' => true
			],
			'faqs-delete-confirmed' => [
				'POST' => [
					'controller' => $faqController,
					'function' => 'faqDeleteConfirmed'
				],
				'login' => true
			]
		];
		
		return $routes;
	}

	public function checkLogin() {
		//session_start();
		if(!isset($_SESSION['loggedin'])) {
			header('location: /');
		}
	}
}