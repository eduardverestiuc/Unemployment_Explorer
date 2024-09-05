<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Get(
 *     path="/admin_obtine_datele.php",
 *     summary="Obține toate datele din sistem (utilizatori, tabele, feedback) pentru partea de admin",
 *     tags={"Admin - Obtine Datele (Users, Tables, Feedback)"},
 * security={{"jwt":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Toate datele solicitate",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="users",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="Lista de utilizatori"
 *             ),
 *             @OA\Property(
 *                 property="tables",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="Lista de tabele din baza de date"
 *             ),
 *             @OA\Property(
 *                 property="feedback",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="message", type="string")
 *                 ),
 *                 description="Feedback-ul utilizatorilor"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Eroare la solicitare"
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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  http_response_code(400);
}

$sql = "SELECT username FROM users";
$result = $conn->query($sql);
$usernames = array();
while($row = $result->fetch_assoc()) {
  $usernames[] = $row['username'];
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = array();
while($row = $result->fetch_row()) {
  $tables[] = $row[0];
}

$sql = "SELECT id, email, message FROM feedback";
$result = $conn->query($sql);
$feedbacks = array();
while($row = $result->fetch_assoc()) {
  $feedbacks[] = $row;
}

$json = array(
  'usernames' => $usernames,
  'tables' => $tables,
  'feedbacks' => $feedbacks
);

header('Content-Type: application/json');
echo json_encode($json);

$conn->close();
?>