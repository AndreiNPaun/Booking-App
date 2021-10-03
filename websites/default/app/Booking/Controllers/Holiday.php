<?php
namespace Booking\Controllers;
class Holiday {
    private $holidayTable;
    private $employeeTable;
    private $webDB;

	public function __construct($holidayTable, $employeeTable, $webDB) {
        $this->holidayTable = $holidayTable;
        $this->employeeTable = $employeeTable;
        $this->webDB = $webDB;
	}

    //Display menu options for holiday
    public function holiday() {
        return [
            'template' => 'holiday.html.php',
            'variables' => [],
            'title' => 'Power-Time - Holiday'
        ];
    }

    //Display holiday booking form
    public function holidayForm() {

        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'employee' || $_SESSION['access_level'] === 'manager' || $_SESSION['access_level'] === 'supervisor') {
            $date = new \DateTime();
            $holiday_date = $date->format('Y-m-d');

            return [
                'template' => 'holidayBook.html.php',
                'variables' => ['holiday_date' => $holiday_date],
                'title' => 'Power-Time - Holiday'
            ];
        }

        else {
            header('location: /');
        }
    }

    //Submit filled form and store into the database
    public function holidaySubmit() {
        $holiday = $_POST['holiday'];

		if ($holiday['start'] !== '' && $holiday['end'] !== '' && $holiday['description'] !== '' && $holiday['employeeID'] !== '' && 
            $holiday['applied_date'] !== '' && $holiday['status'] !== '') {

            $start = \strtotime($holiday['start']);
            $end = \strtotime($holiday['end']);

            $start_format = date('Y-m-d', $start);
            $end_format = date('Y-m-d', $end);
        
            $holiday['start'] = $start_format;
            $holiday['end'] = $end_format;

			$this->holidayTable->save($holiday);

            $message = [0 => 'Holiday Booked.'];
			header('refresh: 4; url=/holiday-display');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Holiday'
			];
        }

        else {
			$message = [0 => 'Empty fields, please try again.'];
			header('refresh: 4; url=/holiday.html.php');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Holiday'
			];
		}
    }

    //Display all holiday applications by category
    public function holidayDisplay() {

        $pending = $this->webDB->holidayDisplayStatus('pending', $_SESSION['loggedin']);
        $accepted = $this->webDB->holidayDisplayStatus('accepted', $_SESSION['loggedin']);
        $rejected = $this->webDB->holidayDisplayStatus('rejected', $_SESSION['loggedin']);
            
        return [
            'template' => 'holidayDisplay.html.php',
            'variables' => ['pending' => $pending, 'accepted'=> $accepted, 'rejected'=> $rejected],
            'title' => 'Power-Time - Display Holiday'
        ];
    }

    //Displays all holiday applications in pending
    public function holidayRequest() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager' ) {
            $holiday = $this->webDB->holidayRequest('pending');
            return [
                'template' => 'admin/holidayRequest.html.php',
                'variables' => ['holiday' => $holiday],
                'title' => 'Power-Time - Holiday'
            ];
        }
        else {
            header('location: /');
        }
    }

    //Displays form with relevant information and two fields which can amend the record on to the database, status and reason
    public function holidayRequestAnswerForm() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager' ) {
            if (isset($_GET['id'])) {
                $holiday = $this->webDB->holidayAnswer($_GET['id'])[0];
                return [
                    'template' => 'admin/holidayAnswer.html.php',
                    'variables' => ['holiday' => $holiday],
                    'title' => 'Power-Time - Holiday'
                ];
            }
            else {
                header('location: /admin-home');
            }
        }
        else {
            header('location: /');
        }
    }
    
    //
    public function holidayRequestAnswerSubmit() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager' ) {
            $holiday = $_POST['holiday'];

            if ($holiday['reason'] !== '' ){
    
                $this->holidayTable->save($holiday);
    
                $message = [0 => 'Holiday Application Amended.'];
                header('refresh: 4; url=/holiday-requests');
                return [
                    'template' => 'applyMessage.html.php',
                    'variables' => ['message' => $message[0]],
                    'title' => 'Power-Time - Holiday'
                ];
        }
            else {
                header('location: /');
            }
        }
    }
}
