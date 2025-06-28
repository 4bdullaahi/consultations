<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/dashboard_error.log');
header('Content-Type: application/json');

class Database {
    private $host = "localhost";
    private $db_name = "healthcare";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}

// Create a global $conn variable for all includes
$db = new Database();
$conn = $db->getConnection();

// Verify the user is logged in and is a patient
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$patient_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    error_log("Connected to DB. Patient ID: $patient_id");

    $stmt = $conn->prepare("CALL GetPatientDashboard(:user_id)");
    $stmt->bindParam(':user_id', $patient_id, PDO::PARAM_INT);
    $stmt->execute();
    error_log("Stored procedure executed.");

    // Fetch all result sets from the stored procedure
    $results = [];
    do {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Fetched result set: " . json_encode($result));
        if ($result) {
            $results[] = $result;
        }
    } while ($stmt->nextRowset());

    error_log("Total result sets: " . count($results));

    // Check if we got the expected number of result sets
    if (count($results) < 6) {
        error_log("Warning: Fewer than 6 result sets returned from stored procedure.");
    }

    // Organize the data into a structured response
    $response = [
        'summary_stats' => [
            'upcoming_appointments' => $results[0][0]['upcoming_appointments'] ?? 0,
            'total_appointments' => $results[0][0]['total_appointments'] ?? 0,
            'total_paid' => $results[0][0]['total_paid'] ?? 0
        ],
        'appointment_status' => $results[1] ?? [],
        'appointment_trend' => $results[2] ?? [],
        'recent_appointments' => $results[3] ?? [],
        'subscription' => $results[4][0] ?? null,
        'spending_breakdown' => $results[5] ?? []
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}