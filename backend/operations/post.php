<?php


while (true) {
    echo "First name: ";
    $firstName = trim(fgets(STDIN));
    if (preg_match('/^[a-zA-Z\s]+$/', $firstName)) {
        break;
    } else {
        echo "Invalid first name (letters/spaces only). Try again.\n";
    }
}


while (true) {
    echo "Last name: ";
    $lastName = trim(fgets(STDIN));
    if (preg_match('/^[a-zA-Z\s]+$/', $lastName)) {
        break;
    } else {
        echo "Invalid last name (letters/spaces only). Try again.\n";
    }
}


while (true) {
    echo "Email: ";
    $email = trim(fgets(STDIN));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $apiUrlCheckEmail = "http://localhost/checkPoint/backend/api/api.php/employees/check_email";
        $chCheckEmail = curl_init($apiUrlCheckEmail);
        curl_setopt($chCheckEmail, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chCheckEmail, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($chCheckEmail, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($chCheckEmail, CURLOPT_POSTFIELDS, json_encode(["email" => $email]));

        $responseCheckEmail = curl_exec($chCheckEmail);
        $httpCodeCheckEmail = curl_getinfo($chCheckEmail, CURLINFO_HTTP_CODE);
        curl_close($chCheckEmail);

        if ($httpCodeCheckEmail === 409) {
            echo "Email already exists. Try again.\n";
        } elseif ($httpCodeCheckEmail === 200) {
            break;
        } else {
            echo "An error occurred while checking the email. Try again.\n";
        }
    } else {
        echo "Invalid email. Try again.\n";
    }
}

while (true) {
    echo "Position: ";
    $position = trim(fgets(STDIN));
    if (!empty($position)) {
        break;
    } else {
        echo "Position cannot be empty. Try again.\n";
    }
}

while (true) {
    echo "Hire date (YYYY-MM-DD): ";
    $hireDate = trim(fgets(STDIN));
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $hireDate, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if (checkdate($month, $day, $year)) {
            break;
        } else {
            echo "Not a real date. Try again (YYYY-MM-DD): ";
        }
    } else {
        echo "Invalid format. Try again (YYYY-MM-DD): ";
    }
}

while (true) {
    echo "Salary: ";
    $salary = trim(fgets(STDIN));
    if (is_numeric($salary)) {
        break;
    } else {
        echo "Salary must be numeric. Try again.\n";
    }
}


$apiUrlPost = "http://localhost/checkPoint/backend/api/api.php/employees";
$chPost = curl_init($apiUrlPost);
curl_setopt($chPost, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chPost, CURLOPT_CUSTOMREQUEST, "POST"); 
curl_setopt($chPost, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($chPost, CURLOPT_POSTFIELDS, json_encode([
    "first_name" => $firstName,
    "last_name"  => $lastName,
    "email"      => $email,
    "position"   => $position,
    "hire_date"  => $hireDate,
    "salary"     => (float)$salary
]));

$responsePost = curl_exec($chPost); 
$httpCodePost = curl_getinfo($chPost, CURLINFO_HTTP_CODE); 
curl_close($chPost); 


echo "\nPOST Response:\n";
echo "HTTP Code: $httpCodePost\n";
echo $responsePost . "\n";
