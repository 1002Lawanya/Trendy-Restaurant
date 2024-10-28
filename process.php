<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "canvila");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process signup
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    $result = $conn->query($sql);

    if ($result) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Process login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user data from the database
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            echo "Login successful!";
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}

$conn->close();
?>
