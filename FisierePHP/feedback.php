<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
/**
 * @OA\Post(
 *     path="/feedback.php",
 *     summary="Primește și înregistrează feedback de la utilizatori",
 *     tags={"Feedback de la Utilizatori"},
 *     @OA\RequestBody(
 *         description="Emailul utilizatorului și mesajul de feedback",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email","mesaj"},
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="Emailul utilizatorului"
 *             ),
 *             @OA\Property(
 *                 property="mesaj",
 *                 type="string",
 *                 description="Mesajul de feedback"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Feedback înregistrat sau limita de feedback atinsă",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de răspuns - succes sau limitaAtinsa"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Date de intrare invalide",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Eroare de server intern",
 *     )
 * )
 */
$host = 'localhost';
$dbname = 'proiect_tehnologii_web';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $data->email]);
    $user = $stmt->fetch();

    if ($user) {
        $today = date('Y-m-d');
        $checkStmt = $conn->prepare("SELECT * FROM feedback WHERE email = :email AND date = :today");
        $checkStmt->execute(['email' => $data->email, 'today' => $today]);
        $feedbackToday = $checkStmt->fetch();

        if (!$feedbackToday) {
            $insertStmt = $conn->prepare("INSERT INTO feedback (email, message, date) VALUES (:email, :message, :date)");
            $insertStmt->execute(['email' => $data->email, 'message' => $data->mesaj, 'date' => $today]);
            echo json_encode(['message' => 'succes']);
        } else {
            echo json_encode(['message' => 'limitaAtinsa']);
            http_response_code(400);
        }
    } else {
        echo json_encode(['message' => 'nuexistaemail']);
        http_response_code(400);
    }
} catch(PDOException $e) {
    echo "Conexiunea la baza de date a eșuat: " . $e->getMessage();
    http_response_code(500);
}
?>