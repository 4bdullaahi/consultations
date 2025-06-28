<?php
header('Content-Type: application/json');
require('apky.php'); // Include your API key file

// Get the POSTed JSON
$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';
if (!$user_message) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

// Your OpenAI API key (keep this secret!)

// Prepare the OpenAI API request
$data = [
    "model" => "gpt-4o-mini",
    "store" => true,
    "messages" => [
        ["role" => "user", "content" => $user_message]
    ]
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . 'Bearer ' . $api_key
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['error' => 'Request Error: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['reply' => $result['choices'][0]['message']['content']]);
} else {
    echo json_encode(['error' => 'Invalid response from OpenAI API', 'raw' => $result]);
}
?>