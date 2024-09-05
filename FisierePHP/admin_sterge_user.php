<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/admin_sterge_user.php",
 *     summary="Șterge un utilizator specificat prin numele de utilizator",
 *     tags={"Admin - Sterge Utilizator"},
 * security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="Numele de utilizator al utilizatorului care trebuie șters",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="username",
 *                 type="string",
 *                 description="Numele de utilizator pentru ștergere"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilizator șters cu succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a operațiunii"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Date de intrare invalide sau utilizatorul nu există",
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

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $username = $data->username;

        $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();

        echo json_encode(array("success" => true));
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "error" => $e->getMessage()));
        http_response_code(500);
    }
    
    $stmt = null;
    $conn = null;
?>