<?php
echo "Enter employee ID (leave empty to fetch all employees): "; 
$id = trim(fgets(STDIN));

$url = "http://localhost/checkPoint/backend/api/api.php/employees"; 
if (!empty($id)) {
    $url .= "/$id";
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
curl_close($ch);

if ($httpCode == 200) {
    echo "GET Response:\n";
    echo $response . "\n";
} elseif ($httpCode == 404) {
    echo "Employee not found.\n";
} else {
    echo "Error: Unable to fetch employees. HTTP Code: $httpCode\n";
}
?>
