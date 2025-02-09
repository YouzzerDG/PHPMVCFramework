<?php namespace Controller;

use App\View;
use Controller\IController;
use Model\Reservation;

class ReservationController implements IController
{

    public function index(): void
    {
        $reservations = Reservation::all();
        
        echo View::render('reservations/index', ['reservations' => $reservations]);
    }

    public function detail($id): void
    {
        $reservation = Reservation::find(['id' => $id]);

        echo View::render('reservations/detail', ['reservation' => $reservation]);
    }

    public function create(): void
    {
        if (!isset($_SESSION ['login'])) {
            header('Location: login.php');
            exit();
        }
        if (isset($_POST['submit'])) {
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $email = $_POST['email'];
            $phoneNumber = $_POST['phone_number'];
            $message = $_POST['message'];
            $startDate = $_POST['start_date'];
            $createdAt = date('Y-m-d H:i:s');
            $serviceId = $_POST['service_id'];

            if (empty($firstName)) {
                $errors['first_name'] = "First name cannot be empty";
            }
            if (empty($lastName)) {
                $errors['last_name'] = "Last name cannot be empty";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Enter a valid email address";
            }
            if (empty($phoneNumber)) {
                $errors['phone_number'] = "Phone number cannot be empty";
            }
            if (empty($serviceId)) {
                $errors['service_id'] = "You must select a service.";
            }

            if (empty($errors)) {
                $contactQuery = "INSERT INTO contacts (first_name, last_name, email, phone_number) 
                         VALUES ('$firstName', '$lastName', '$email', '$phoneNumber')";
                $contactResult = mysqli_query($db, $contactQuery);

                if ($contactResult) {
                    $contactId = mysqli_insert_id($db);

                    $reservationQuery = "INSERT INTO reservations (contact_id, message, start_date, created_at, service_id) 
                                 VALUES ($contactId, '$message', '$startDate', '$createdAt', $serviceId)";
                    $reservationResult = mysqli_query($db, $reservationQuery);

                    if ($reservationResult) {
                        header('Location: index.html');
                        exit();
                    } else {
                        $errors['db'] = "Error inserting into reservations table.";
                    }
                } else {
                    $errors['db'] = "Error inserting into contact table.";
                }
            }
        }
    }

    public function edit($id): void
    {
        $reservation = Reservation::find(['id' => $id]);

        echo View::render('reservations/edit', ['reservation' => $reservation]);
    }

    public function update($id): void
    {
        // TODO: Implement update() method.
    }

    public function delete($id): void
    {
        // TODO: Implement delete() method.
    }
}