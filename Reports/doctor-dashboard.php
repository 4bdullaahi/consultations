<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');


$dsn = 'mysql:host=localhost;dbname=healthcare;charset=utf8mb4';
$user = 'root';
$pass = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]; // Make sure this file sets $dsn, $user, $pass, $options

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => "Database connection failed: " . $e->getMessage()]);
    exit;
}

try {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
        exit;
    }

    $stmt = $pdo->prepare("CALL GetDoctorDashboard(?)");
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();

    $results = [];
    $results['summary'] = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['payment_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['monthly_trends'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['daily_trends'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['recent_appointments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['top_patients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->nextRowset();
    $results['time_slots'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map monthly_trends to expected keys
    $monthlyData = [];
    foreach ($results['monthly_trends'] as $row) {
        $monthlyData[] = [
            'month' => $row['month'] ?? '',
            'scheduled' => isset($row['total']) ? (int)$row['total'] : 0, // <-- map 'total' to 'scheduled'
            'completed' => isset($row['completed']) ? (int)$row['completed'] : 0,
            'cancelled' => isset($row['cancelled']) ? (int)$row['cancelled'] : 0,
        ];
    }

    $mapped = [
        'summary' => $results['summary'],
        'monthlyData' => $monthlyData,
        'paymentData' => [
            'paid' => 0,
            'pending' => 0,
            'failed' => 0
        ],
        'flow' => [
            'scheduled' => 0,
            'completed' => 0,
            'paid' => 0
        ],
        'recentAppointments' => $results['recent_appointments']
    ];

    // status_distribution is fine
    foreach ($results['status_distribution'] as $row) {
        if (!isset($row['status']) || $row['status'] === null) continue;
        $status = strtolower($row['status']);
        $count = (int)$row['count'];
        if ($status === 'scheduled') $mapped['flow']['scheduled'] = $count;
        if ($status === 'completed') $mapped['flow']['completed'] = $count;
        if ($status === 'paid') $mapped['flow']['paid'] = $count;
    }

    // payment_distribution: use 'payment_status' instead of 'status'
    foreach ($results['payment_distribution'] as $row) {
        if (!isset($row['payment_status']) || $row['payment_status'] === null) continue;
        $status = strtolower($row['payment_status']);
        $count = (int)$row['count'];
        if ($status === 'paid') $mapped['paymentData']['paid'] = $count;
        if ($status === 'pending') $mapped['paymentData']['pending'] = $count;
        if ($status === 'failed') $mapped['paymentData']['failed'] = $count;
    }

    // Incorporating suggested code change
    $mapped['summary']['doctor_name'] = 'abdullaahi';

    // debug
    // file_put_contents('debug_dashboard.json', json_encode($mapped, JSON_PRETTY_PRINT));

    echo json_encode($mapped);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}