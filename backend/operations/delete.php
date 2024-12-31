<?php
echo "Enter the ID of the employee to delete: ";
$id = trim(fgets(STDIN));

if (empty($id) || !is_numeric($id)) {
    echo "Invalid ID. Please enter a valid numeric ID.\n";
    exit;
}

$url = "http://localhost/checkPoint/backend/api/api.php/employees";

$data = json_encode(["id" => $id]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
curl_setopt($ch, CURLOPT_HTTPHEADER, [ 
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 


$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


if ($response === false) {
    echo "Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit;
}

curl_close($ch);


echo "DELETE Response:\n";
echo "HTTP Code: $httpCode\n";
echo $response . "\n";
?>
