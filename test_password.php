<?php
$hashedPassword = '$2y$10$kE9yH1P6a2zbgXNzzZOh0Oi6LwvE5OoyT1VURFG9YTA70RrxHyuaS'; // From your database
$enteredPassword = 'password123'; // The password you are testing

if (password_verify($enteredPassword, $hashedPassword)) {
    echo "Password matches!";
} else {
    echo "Password does not match.";
}
?>
