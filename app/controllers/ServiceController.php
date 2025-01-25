<?php

namespace Controller;

use App\View;
use Controller\IController;

class ServiceController implements IController
{

    public function index(): void
    {
        echo View::render("services/index");
    }

    public function show($id): void
    {
        if (!isset($_SESSION ['login'])){
            header('Location: login.php');
            exit();
        }
        $id = mysqli_escape_string($db, $_GET['id']);
        $errors = [];

        $query = "SELECT name, price, page_content, url, image FROM services WHERE id = $id";
        $result = mysqli_query($db, $query);
        $services = mysqli_fetch_assoc($result);

        $name = $services['name'];
        $price = $services['price'];
        $pageContent = $services['page_content'];
        $url = $services['url'];
        $image = $services['image'].
    }

    public function create(): void
    {
        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $pageContent = $_POST['page_content'];
            $url = $_POST['url'];
            $image = $_FILES['image'];

            if (empty($name)) {
                $errors['name'] = "Name cannot be empty";
            }
            if (empty($price) || !is_numeric($price)) {
                $errors['price'] = "Price must be a valid number";
            }
            if (empty($pageContent)) {
                $errors['page_content'] = "Page content must be filled";
            }
            if (empty($url)) {
                $errors['url'] = "URL cannot be empty";
            }
            if ($image['error'] !== UPLOAD_ERR_OK) {
                $errors['image'] = "Image upload failed.";
            } else {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!in_array($image['type'], $allowedTypes)) {
                    $errors['image'] = "Only JPG, PNG, and GIF files are allowed.";
                }
            }

            if (empty($errors)) {
                $uploadDir = 'uploads/';
                $fileName = time() . '_' . basename($image['name']);
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($image['tmp_name'], $filePath)) {
                    $query = "INSERT INTO services (name, price, page_content, url, image) 
                      VALUES ('$name', $price, '$pageContent', '$url', '$fileName')";
                    $result = mysqli_query($db, $query);

                    if ($result) {
                        header('Location: services.php');
                        exit();
                    } else {
                        $errors['db'] = "Error inserting service: " . mysqli_error($db);
                    }
                } else {
                    $errors['image'] = "Failed to save the uploaded image.";
                }
            }
        }
    }

    public function update($id): void
    {
        if (!isset($_SESSION ['login'])){
            header('Location: login.php');
            exit();
        }
        $id = mysqli_escape_string($db, $_GET['id']);
        $errors = [];

        $query = "SELECT name, price, page_content, url, image FROM services WHERE id = $id";
        $result = mysqli_query($db, $query);
        $services = mysqli_fetch_assoc($result);

        $name = $services['name'] ?? '';
        $price = $services['price'] ?? '';
        $pageContent = $services['page_content'] ?? '';
        $url = $services['url'] ?? '';
        $image = $services['image'] ?? '';

        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $pageContent = $_POST['page_content'];
            $url = $_POST['url'];

            if (empty($name)) {
                $errors['name'] = "Name cannot be empty";
            }
            if (empty($price)) {
                $errors['price'] = "Synopsis cannot be empty";
            }
            if (empty($pageContent)) {
                $errors['page_content'] = "Episodes must be a valid number";
            }
            if (empty($url)) {
                $errors['url'] = "Release date must be a valid year";
            }
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = 'uploads/';
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                    $newImage = $fileName;
                } else {
                    $errors['image'] = "Failed to upload new image.";
                }
            } else {
                $newImage = $image;
            }

            if (empty($errors)) {
                $updateQuery = "UPDATE services 
                        SET name = '$name', price = $price, page_content = '$pageContent', url = '$url', image = '$newImage' 
                        WHERE id = $id";
                $updateResult = mysqli_query($db, $updateQuery);

                if ($updateResult) {
                    header('Location: servicesDetail.php?id=' . $id);
                    exit();
                } else {
                    $errors['db'] = "Error updating service: " . mysqli_error($db);
                }
            }
        }
    }

    public function delete($id): void
    {
        if (!isset($_SESSION ['login'])){
            header('Location: login.php');
            exit();
        }
        $id = $_GET['id'];

        if (isset($id)) {
            $query = "DELETE FROM services WHERE id = $id";
            $result = mysqli_query($db, $query);
            var_dump($result);

            if ($result) {
                header('Location: index.html');
                exit();
            } else {
                echo "Error deleting record.";
            }
        } else {
            header('Location: services.php');
            exit();
        }
    }
}