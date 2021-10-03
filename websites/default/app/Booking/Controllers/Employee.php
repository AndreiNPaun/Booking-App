<?php
namespace Booking\Controllers;
class Employee {
	private $employeeTable;

	public function __construct($employeeTable) {
		$this->employeeTable = $employeeTable;
	}

    //Display home page content
    public function home() {
		$employee = $this->employeeTable->find('employeeID', 1);

		return [
			'template' => 'home.html.php',
			'variables' => ['employee' => $employee[0]],
			'title' => 'Power-Time'
		];
	}

    //Upload all the user input from the register form into the database
    public function registerSubmit() {
        
        $employee = $_POST['employee'];
            
        //Form cannot be submited if fields are empty
        if ($employee['firstname'] !== '' && $employee['lastname'] !=='' && $employee['email'] !== '' && $employee['password'] !== '' && $employee['dob'] !== '' && 
            $employee['phone'] !== '' && $employee['address'] !== '') {
            
            //Check if the submited email is of format email@email.com and if not redirect back to register page
            if (!filter_var($employee['email'], FILTER_VALIDATE_EMAIL)) {
                $message = [0 => 'Invalid email, redirecting to register page.'];
                return [
                    'template' => 'applyMessage.html.php',
                    'variables' => ['message' => $message[0]],
                    'title' => 'Power-Time - Register'
                ];
                header('refresh: 3; url = register');       
            }
            else {       
                //Password hashing for additional protection on accounts
                $hash = password_hash($employee['password'], PASSWORD_DEFAULT);
                $employee['password'] = $hash;

                $date = \strtotime($employee['dob']);

                $date_format = date('Y-m-d', $date);
            
                $employee['dob'] = $date_format;
                
                $this->employeeTable->save($employee);

                //After successfuly registering, user will be taken to login page
                $message = [0 => 'Account created.'];

                header('refresh: 3; url = login');

                return [
                    'template' => 'applyMessage.html.php',
                    'variables' => ['message' => $message[0]],
                    'title' => 'Power-Time - Register'
                ];
            }
        }
            //Error message in case fields are not completed
        else {
            $message = [0 => 'All fields must be completed.'];
            return [
                'template' => 'applyMessage.html.php',
                'variables' => ['message' => $message[0]],
                'title' => 'Power-Time - Register'
            ];
            header('refresh: 3; url = register');
        }
    }

    //Display registration form
    public function registerForm() {

        if (!isset($_SESSION['loggedin'])){
            return [
                'template' => 'register.html.php',
                'title' => 'Power-Time - Register',
                'variables' => []
            ];
        }

        //if User is already logged in, redirect to homepage
        else{
            header('location: /');
        }
    }

    //Submits and stores into a temporary variable the user input data and runs a check to confirm user account profile
    public function loginSubmit() {

        $login = $_POST['employee'];

        if ($login['email'] !== '' && $login['password'] !== '') {
            //DB query to find out if the user trying to log in actually exists
            $employee = $this->employeeTable->find('email', $login['email'])[0];

            //Log in validation related to the query above
            if ($employee['email'] === $login['email']) {
                    
                //Heads back to index.php after the log in has been successful
                if (password_verify($login['password'], $employee['password'])) {
                    $_SESSION['loggedin'] = $employee['employeeID'];
                    $_SESSION['access_level'] = $employee['access_level'];
                    $_SESSION['firstname'] = $employee['firstname'];
                    $_SESSION['lastname'] = $employee['lastname'];
                    $_SESSION['email'] = $employee['email'];
                    
                    if ($_SESSION['access_level'] === 'admin'|| $_SESSION['access_level'] === 'manager' || $_SESSION['access_level'] === 'supervisor') {
                        header('location: /admin-home');
                    }
                    else {
                        header('location: /');
                    }
                }
                else{
                    $message = [0 => 'Incorrect details, please try again.'];
                    return [
                        'template' => 'applyMessage.html.php',
                        'variables' => ['message' => $message[0]],
                        'title' => 'Power-Time - Login'
                    ];
                    header('refresh: 3; url= /login');
                }
            }
            //If fields are empty or details are incorrect, refresh page
            else{
                $message = [0 => 'Incorrect details, please try again.'];
                return [
                    'template' => 'applyMessage.html.php',
                    'variables' => ['message' => $message[0]],
                    'title' => 'Power-Time - Login'
                ];
                header('refresh: 3; url= /login');
            }      
        }

        else {
            $message = [0 => 'Fields must be completed.'];
            return [
                'template' => 'applyMessage.html.php',
                'variables' => ['message' => $message[0]],
                'title' => 'Power-Time - Login'
            ];
            header('refresh: 3; url= /login');
        }  
    }

    public function loginForm() {
        if (!isset($_SESSION['loggedin'])){

            return [
			    'template' => 'login.html.php',
			    'variables' => [],
			    'title' => 'Power-Time - Login'
            ];
        }

        else {
            header('location: /');
        }
    }

    //Logs the user out of the website
    public function logout() {

        //Unsets session variables for user, making logout possible.
        unset($_SESSION['loggedin']);
        unset($_SESSION['access_level']);
        unset($_SESSION['firstname']);
        unset($_SESSION['lastname']);
        unset($_SESSION['email']);

        //Heads back to login after user successfully logged out.
        header('location: /login');
    }

    //Displays admin homepage with all relevant links
    public function adminHome() {
        if(isset($_SESSION['loggedin']) && $_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager' ||$_SESSION['access_level'] === 'supervisor') {
            return [
                'template' => 'admin/admin.html.php',
                'variables' => [],
                'title' => 'Power-Time - Admin'
            ];
        }

        else {
            header('location: /');
        }
    }

    //Display list of registed employees
    public function employeeList() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager' || $_SESSION['access_level'] === 'supervisor') {
            $employee = $this->employeeTable->findAll();

            return [
                'template' => 'admin/employeeList.html.php',
                'variables' => ['employee' => $employee],
                'title' => 'Power-Time - Users'
            ];
        }
        else {
            header('location: /');
        }
    }

    //Employee profile edit submit with any changes made to the profile by the admin
    public function employeeEditAdminSubmit() {
		$employee = $_POST['employee'];

		if ($employee['firstname'] !== '' && $employee['lastname'] !== '' && $employee['email'] !== '' && $employee['password'] !== '' && 
            $employee['phone'] !== '' && $employee['address'] !== '') {

			$this->employeeTable->save($employee);
			header('location: /employees-list');
		}

		else {
			$message = [0 => 'Empty fields, please try again.'];
			header('refresh: 4; url=/employees-list');
			return [
				'template' => 'admin/addMessage.html.php',
				'variables' => ['message' => $message[0]],
				'title' => 'Power-Time - Edit Job'
			];
		}
	}

    //Displays a form with all the user data stored in the emnployee table, making it changable
    public function employeeEditFormAdmin() {
		if ($_SESSION['access_level'] === 'admin') {

			if (isset($_GET['id'])) {
				$record = $this->employeeTable->find('employeeID', $_GET['id'])[0];
			}
			else {
				$record = false;
			}

			return [
				'template' => 'admin/employeeEdit.html.php',
				'variables' => ['record' => $record],
				'title' => 'Power-Time - Edit Job'
			];
		}

		else {
			header('location: /');
		}
	}

    //Display singular employee record based on the link clicked
    public function employeeDisplay() {
        if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager' || $_SESSION['access_level'] === 'supervisor') {
            if (isset($_GET['id'])) {
                $record = $this->employeeTable->find('employeeID', $_GET['id'])[0];

                return [
                    'template' => 'admin/employeeDisplay.html.php',
                    'variables' => ['record' => $record],
                    'title' => 'Power-Time - Display Record'
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
}