<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/admin_sterge_feedback.php",
 *     summary="Șterge un feedback specificat prin ID",
 *     tags={"Admin - Sterge Feedback"},
 * security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="ID-ul feedback-ului care trebuie șters",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="ID-ul feedback-ului pentru ștergere"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Feedback șters cu succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de succes"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Date de intrare invalide",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de eșec a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Mesajul de eroare"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Eroare de server intern",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de eșec a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Mesajul de eroare"
 *             )
 *         )
 *     )
 * )
 */
$key = "secret_key";

if (!isset(getallheaders()['Authorization'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Acces neautorizat."));
    exit;
}

$authHeader = getallheaders()['Authorization'];
list($jwt) = sscanf($authHeader, 'Bearer %s');

if ($jwt) {
  try {
      $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

      if (isset($decoded->role) && $decoded->role === 'admin') {
    } else {
        http_response_code(403);
        echo json_encode(array("message" => "Acces restricționat. Necesită rolul de admin."));
        exit;
    }
  } catch (Exception $e) {
      http_response_code(401);
      echo json_encode(array("message" => "Acces neautorizat. Token invalid."));
      exit;
  }
} else {
  http_response_code(401); // Unauthorized
  echo json_encode(array("message" => "Acces neautorizat. Token lipsă."));
  exit;
}
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "proiect_tehnologii_web";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die(json_encode(array("success" => false, "error" => "Connection failed: " . $e->getMessage())));
        http_response_code(400);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $feedbackId = $data->id;

    if (!isset($feedbackId)) {
        die(json_encode(array("success" => false, "error" => "Invalid input data")));
        http_response_code(400);
    }

    try {
        $stmt = $conn->prepare("DELETE FROM feedback WHERE id = :feedbackId");
        $stmt->bindParam(':feedbackId', $feedbackId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(array("success" => true, "message" => "Feedback deleted successfully"));
        } else {
            die(json_encode(array("success" => false, "error" => "Feedback not found or could not be deleted")));
            http_response_code(400);
        }
    } catch(PDOException $e) {
        die(json_encode(array("success" => false, "error" => "Error executing query: " . $e->getMessage())));
        http_response_code(500);
    }

    $conn = null;
?>