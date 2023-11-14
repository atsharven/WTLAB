<?php
// Database configuration
$dsn = 'mysql:host=localhost;dbname=entry';
$username = 'root';
$password = ''; // No password for localhost

try {
    // Connect to the database
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '';
        $address = $_POST['address'];
        $password = password_hash($_POST['pass'], PASSWORD_DEFAULT); // Hash the password

        // Prepare and execute the SQL statement to insert data into the database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $address, $password]);

        // Display success message or handle further actions
        echo '<div class="alert alert-success" role="alert">
                Registration successful! Data stored in the database.
              </div>';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script>
        function validatePhoneNumber() {
            var phoneNumber = document.getElementById('phoneNumber').value;
            var phoneNumberError = document.getElementById('phoneNumberError');
            var phoneNumberPattern = /^\+91\d{10}$/;

            if (phoneNumberPattern.test(phoneNumber)) {
                phoneNumberError.innerHTML = '';
                return true;
            } else {
                phoneNumberError.innerHTML = 'Enter a valid phone number with +91 and 10 digits.';
                return false;
            }
        }

        function validatePassword() {
            var password = document.getElementById('pass').value;
            var messageElement = document.getElementById('passwordMessage');

            if (password.length === 0) {
                messageElement.innerHTML = '';
            } else if (password.length < 8) {
                messageElement.innerHTML = 'Password must be at least 8 characters long.';
                return false; // Prevent form submission
            } else if (!/[A-Z]/.test(password)) {
                messageElement.innerHTML = 'Password must start with a capital letter.';
                return false;
            } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                messageElement.innerHTML = 'Password must contain at least one special character.';
                return false;
            } else {
                messageElement.innerHTML = '';
            }

            return true; // Allow form submission
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h1>Login Page</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '';
        $address = $_POST['address'];
        $password = $_POST['pass'];
        $confirmPassword = $_POST['confirmPassword'];

        // Validate name (at least 10 characters)
        if (strlen($name) < 10) {
            echo '<div class="alert alert-danger" role="alert">
                  Name must be at least 10 characters.
              </div>';
        } else {
            // Validate email, phone, address (you can add more specific validations)
            // ...

            // Validate password and confirm password
            if (!preg_match('/^(?=.*[A-Z].*[A-Z])(?=.*[0-9])(?=.*[_@$])[a-zA-Z0-9_@$]{2,}$/', $password)) {
                echo '<div class="alert alert-danger" role="alert">
                      Password must have at least 2 uppercase letters, 1 digit, 1 of _@$ and be at least 2 characters long.
                  </div>';
            } elseif ($password !== $confirmPassword) {
                echo '<div class="alert alert-danger" role="alert">
                      Password and Confirm Password do not match.
                  </div>';
            } else {
                // All validations passed, store data in an associative array
                $userData = array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'password' => $password
                );

                // Display success message or store data in a database
                echo '<div class="alert alert-success" role="alert">
                      Registration successful! Data stored:
                      <pre>' . print_r($userData, true) . '</pre>
                  </div>';
            }
        }
    }
    ?>

    <!-- Login form -->
    <form action="/ats/folder/form.php" method="post" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="name" class="form-label">Name (at least 10 characters)</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
         <label for="phoneNumber" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" oninput="validatePhoneNumber()">
        <div id="phoneNumberError" class="form-text text-muted"></div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <div class="mb-3">
        <label for="pass" class="form-label">Password</label>
        <input type="password" class="form-control" id="pass" name="pass" oninput="validatePassword()">
        <div id="passwordMessage" class="form-text text-muted"></div>
        </div>
        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<!-- Optional JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
