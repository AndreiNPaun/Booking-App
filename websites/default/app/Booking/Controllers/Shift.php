<?php
namespace Booking\Controllers;
class Shift {
    private $shiftTable;
	private $employeeTable;
    private $webDB;
    private $holidayTable;

	public function __construct($employeeTable, $shiftTable, $webDB, $holidayTable) {
		$this->employeeTable = $employeeTable;
        $this->shiftTable = $shiftTable;
        $this->webDB = $webDB;
        $this->holidayTable = $holidayTable;
	}

    //Create shift
    public function shiftSubmit() {
        $shift = $_POST['shift'];

		if ($shift['shift_date'] !== '' && $shift['begins'] !== '' && $shift['finish'] !== '' && $shift['break_start'] !== '' && 
            $shift['break_finish'] !== '') {

            $date = new \DateTime();
            $holiday_date = $date->format('Y-m-d');
            $date = \strtotime($shift['shift_date']);

            $date_format = date('Y-m-d', $date);

            $shift['shift_date'] = $date_format;

			$this->shiftTable->save($shift);

            $message = [0 => 'Shift Created.'];
			header('refresh: 4; url=/admin-home');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Shift'
			];
		}

		else {
			$message = [0 => 'Empty fields, please try again.'];
			header('refresh: 4; url=/shift');
			return [
				'template' => 'applyMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Shift'
			];
		}
    }

    //Display shift creation form
    public function shiftForm() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager') {
            $employee = $this->employeeTable->findAll();
            return [
                'template' => 'admin/shiftBooking.html.php',
                'variables' => ['employee' => $employee],
                'title' => 'Power-Time - Shift'
            ];
        }
        else {
            header('location: /');
        }
    }

    //Display all shifts for a specific day
    public function shiftDisplay() {

        $shiftDate = $_POST['shift'];

        $date = \strtotime($shiftDate['shift_date']);
        $date_format = date('Y-m-d', $date);

        $shiftDate['shift_date'] = $date_format;
		$shifts = $this->webDB->shiftEmployee($shiftDate['shift_date']);

            return [
                'template' => 'admin/shiftDisplay.html.php',
                'variables' => ['shifts' => $shifts, 'shiftDate'=> $shiftDate],
                'title' => 'Power-Time - Display Shift'
            ];
	}

    //Form asking for the date the staff wishes to display all the booked shifts from
    public function shiftDisplayForm() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager') {
            $employee = $this->employeeTable->findAll();

            return [
                'template' => 'admin/shiftDisplayForm.html.php',
                'variables' => ['employee' => $employee],
                'title' => 'Power-Time - Shift'
            ];
        }
        else {
            header('location: /');
        }
    }

    //Confirm delete form, asking the users if they are sure on deleting the booked shift or not
    public function deleteConfirm() {
        if (isset($_GET['id'])) {
            $shift = $this->webDB->shiftDeleteConf($_GET['id'])[0];

            return [
                'template' => 'admin/shiftDeleteConf.html.php',
                'variables' => ['shift' => $shift],
                'title' => 'Power-Time -  Shift Delete'
            ];
        }

        else {
            header('location: /admin-home');
        }
    }

    //After user confirmed shift deletion, delete it
	public function deleteShift() {
		$this->shiftTable->delete($_POST['id']);

        $message = [0 => 'Shift Record Deleted.'];
        header('refresh: 4; url=/admin-home');
        return [
            'template' => 'applyMessage.html.php',
            'variables' => ['message' => $message[0]],
            'title' => 'Power-Time - Shift'
        ];
	}

    //Display logged on user's booked in shifts with the date equal to today's or greater
    public function shiftList() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
            $shift = $this->webDB->shiftList($_SESSION['loggedin']);
            
            return [
                'template' => 'shiftList.html.php',
                'variables' => ['shift' => $shift],
                'title' => 'Power-Time -  Shifts'
            ];
        }

        else {
            header('location: /');
        }
    }

}