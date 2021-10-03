<?php
namespace Booking\Database;
Class WebDB {
    private $pdo;
	private $table;

	public function __construct($pdo, $table) {
		$this->pdo = $pdo;
		$this->table = $table;
	}

    //Find shift by merging employee table with shift table
    public function findShift($value) {
        $stmt = $this->pdo->prepare('SELECT * FROM shift WHERE shift_date = :shift_date');
        $criteria = [
            'shift_date' => $value
        ];
        $stmt->execute($criteria);

        return $stmt->execute($criteria);
    }

    public function shiftEmployee($date) {
        $stmt = $this->pdo->prepare(' SELECT e.employeeID, e.firstname, e.lastname, e.email, e.phone , s.shiftID, s.shift_date, s.begins, s.finish, s.break_start, s.break_finish
                                        FROM employee e
                                        INNER JOIN shift s ON s.employeeID = e.employeeID
                                        WHERE s.shift_date = :date');

        $criteria = [
            'date' => $date
        ];
        $stmt->execute($criteria);

        return $stmt->fetchAll();
    }

    //Code for holiday tracking, use if bug fixed
    /*public function shiftBooking() {
        $stmt = $this->pdo->prepare(' SELECT e.employeeID, e.firstname, e.lastname, e.email, e.phone , s.shiftID, s.shift_date, s.begins, s.finish, s.break_start, s.break_finish, 
                                                h.holidayID, h.start, h.end, h.status
                                        FROM employee e
                                        LEFT JOIN shift s ON s.employeeID = e.employeeID
                                        LEFT JOIN holiday h ON h.employeeID = s.employeeID');

        $stmt->execute();

        return $stmt->fetchAll();
    }*/

    //Confirmation validation for deleting booked shift
    public function shiftDeleteConf($id) {
        $stmt = $this->pdo->prepare(' SELECT e.firstname, e.lastname, s.shiftID, s.shift_date, s.begins, s.finish, s.break_start, s.break_finish
                                        FROM employee e
                                        INNER JOIN shift s ON s.employeeID = e.employeeID
                                        WHERE s.shiftID = :id');

        $criteria = [
            'id' => $id
        ];
        $stmt->execute($criteria);

        return $stmt->fetchAll();
    }

    //Displays the category of the holiday that has been marked by staff (refused, pending, aproved)
    public function holidayDisplayStatus($status, $id) {
        $stmt = $this->pdo->prepare(' SELECT e.employeeID, e.firstname, e.lastname, h.holidayID, h.employeeID, h.applied_date, h.start, h.end, h.description, h.status, h.reason
                                        FROM employee e
                                        INNER JOIN holiday h ON h.employeeID = e.employeeID
                                        WHERE h.employeeID = :id AND h.status = :status');

        $criteria = [
            'id' => $id,
            'status' => $status
        ];
        $stmt->execute($criteria);

        return $stmt->fetchAll();
    }

    //Test Query, no impact on the actual app
    public function test() {
        $stmt = $this->pdo->prepare(' SELECT e.firstname, e.lastname, s.shiftID, s.shift_date, s.start, s.finish, s.break_start, s.break_finish, h.holidayID, h.employeeID, h.start, h.end
                                        FROM employee e
                                        INNER JOIN shift s ON s.employeeID = e.employeeID
                                        INNER JOIN holiday h
                                        WHERE s.shiftID = :id');
        $criteria = [
            'id' => $id,
            'status' => $status
        ];
        $stmt->execute($criteria);

        return $stmt->fetchAll();   
    }

    //
    public function shiftList($id) {
        $date = new \DateTime();
        $dateFormat = $date->format('Y-m-d');

		$stmt = $this->pdo->prepare('SELECT * FROM shift WHERE employeeID = :id AND shift_date > :date ORDER BY shift_date');
		$values = [
			'date' => $dateFormat,
            'id' => $id
		];
		$stmt->execute($values);
		return $stmt->fetchAll();
    }

    public function holidayRequest($status) {
        $stmt = $this->pdo->prepare('SELECT * FROM holiday h
                                    LEFT JOIN employee e ON e.employeeID = h.employeeID
                                    WHERE h.status = :status ORDER BY applied_date');
        
        $values = [
			'status' => $status
		];

        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function holidayAnswer($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM holiday h
                                    LEFT JOIN employee e ON e.employeeID = h.employeeID
                                    WHERE h.holidayID = :id');
        
        $values = [
			'id' => $id
		];

        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function enquiryAnswer() {
        $stmt = $this->pdo->prepare('SELECT * FROM enquiry en
                                    LEFT JOIN employee e ON e.employeeID = en.employeeID');
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function enquirySolve($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM enquiry en
                                    LEFT JOIN employee e ON e.employeeID = en.employeeID
                                    WHERE en.enquiryID = :id ORDER BY en.post_date');
        
        $values = [
            'id' => $id
        ];
                            
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function enquiryFindArchived($status) {
        $stmt = $this->pdo->prepare('SELECT * FROM enquiry en
                                    LEFT JOIN employee e ON e.employeeID = en.employeeID
                                    WHERE en.status = :status');
        
        $values = [
            'status' => $status
        ];
                            
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function enquiryDisplayForms($id, $status) {
        $stmt = $this->pdo->prepare('SELECT * FROM enquiry en
                                    LEFT JOIN employee e ON e.employeeID = en.employeeID
                                    WHERE en.employeeID = :id AND en.status = :status');
        
        $values = [
            'id' => $id,
            'status' => $status
        ];
                            
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function enquiryDeleteCheck($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM enquiry en
                                    LEFT JOIN employee e ON e.employeeID = en.employeeID
                                    WHERE en.enquiryID = :id');
        
        $values = [
            'id' => $id
        ];
                            
        $stmt->execute($values);
        return $stmt->fetchAll();
    }
}
	