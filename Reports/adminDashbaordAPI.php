<?php
// dashboard_api.php

header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'healthcare';
$username = 'root';
$password = '';

try {
    // Create database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Call the stored procedure
    $stmt = $conn->prepare("CALL admin_dashboard_rp()");
    $stmt->execute();

    // Fetch all results (since stored procedure returns multiple result sets)
    $results = [];
    do {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $results[] = $result;
        }
    } while ($stmt->nextRowset());

    // Organize the data into a structured response
    $response = [
        'user_stats' => $results[0][0] ?? [],
        'doctor_stats' => $results[1][0] ?? [],
        'appointment_stats' => $results[2][0] ?? [],
        'appointment_distribution' => $results[3] ?? [],
        'revenue_trend' => $results[4] ?? [],
        'subscription_analytics' => $results[5] ?? [],
        'recent_payments' => $results[6] ?? []
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>