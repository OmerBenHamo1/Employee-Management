<?php

while (true) {
    echo "Enter the ID of the employee to update: ";
    $id = trim(fgets(STDIN));

    if (ctype_digit($id)) {
        break;
    } else {
        echo "Invalid ID, please try again.\n";
    }
}



$apiUrlGet = "http://localhost/checkPoint/backend/api/api.php/employees/" . $id;
$chGet = curl_init($apiUrlGet);
curl_setopt($chGet, CURLOPT_RETURNTRANSFER, true);
$responseGet = curl_exec($chGet);
$httpCodeGet = curl_getinfo($chGet, CURLINFO_HTTP_CODE);
curl_close($chGet);

if ($httpCodeGet !== 200) {
    echo "Error retrieving employee (HTTP code $httpCodeGet): $responseGet\n";
    exit;
}

$existingData = json_decode($responseGet, true);
if (!$existingData) {
    echo "Failed to decode existing employee data. Response was: $responseGet\n";
    exit;
}


$fieldsMap = [
    1 => 'first_name',
    2 => 'last_name',
    3 => 'email',
    4 => 'position',
    5 => 'hire_date',
    6 => 'salary'
];

    while (true) {
    echo "\nWhich field do you want to update?\n";
    foreach ($fieldsMap as $num => $fieldName) {
        echo "  $num) $fieldName (current: {$existingData[$fieldName]})\n";
    }
    echo "Choose a number (or press ENTER to skip updating anything else): ";

    $choice = trim(fgets(STDIN));

    if ($choice === '') {
        return;
    }

    if (!isset($fieldsMap[$choice])) {
        echo "Invalid choice, please try again.\n";
        continue; 
    }

    $fieldToUpdate = $fieldsMap[$choice];
    echo "Enter new value for $fieldToUpdate: ";
    $newValue = trim(fgets(STDIN));

    switch ($fieldToUpdate) {
        case 'first_name':
        case 'last_name':
            while (!preg_match('/^[a-zA-Z\s]+$/', $newValue)) {
                echo "Invalid $fieldToUpdate (letters/spaces only). Try again: ";
                $newValue = trim(fgets(STDIN));
            }
            break;
        case 'email':
            while (!filter_var($newValue, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email. Try again: ";
                $newValue = trim(fgets(STDIN));
            }
            break;
        case 'position':
            while (empty($newValue)) {
                echo "Invalid position (cannot be empty). Try again: ";
                $newValue = trim(fgets(STDIN));
            }
            break;
        case 'hire_date':
            while (true) {
                if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $newValue, $m)) {
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
                $newValue = trim(fgets(STDIN));
            }
            break;
        case 'salary':
            while (!is_numeric($newValue)) {
                echo "Salary must be numeric. Try again: ";
                $newValue = trim(fgets(STDIN));
            }
            break;
    } }

    $existingData[$fieldToUpdate] = $newValue;

    echo "Field '$fieldToUpdate' updated!\n";

$apiUrlPut = "http://localhost/checkPoint/backend/api/api.php/employees/$id";
$chPut = curl_init($apiUrlPut);
curl_setopt($chPut, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chPut, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($chPut, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($chPut, CURLOPT_POSTFIELDS, json_encode([
    "first_name" => $existingData['first_name'],
    "last_name"  => $existingData['last_name'],
    "email"      => $existingData['email'],
    "position"   => $existingData['position'],
    "hire_date"  => $existingData['hire_date'],
    "salary"     => (float)$existingData['salary']
]));

$responsePut = curl_exec($chPut);
$httpCodePut = curl_getinfo($chPut, CURLINFO_HTTP_CODE);
curl_close($chPut);

echo "\nPUT Response:\n";
echo "HTTP Code: $httpCodePut\n";
echo $responsePut . "\n";
