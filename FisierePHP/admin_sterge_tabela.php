<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/admin_sterge_tabela.php",
 *     summary="Șterge o tabelă specificată prin numele său din baza de date",
 *     tags={"Admin - Sterge Tabela Din Baza de Date"},
 *     security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="Numele tabelului care trebuie șters",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="tableName",
 *                 type="string",
 *                 description="Numele tabelului pentru ștergere"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tabel șters cu succes",
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
 *         description="Date de intrare invalide sau tabelul nu există",
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
        http_response_code(500);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $tableName = $data->tableName;

    if (!isset($tableName)) {
        die(json_encode(array("success" => false, "error" => "Invalid input data")));
    }

    try {
        $result = $conn->query("SHOW TABLES LIKE '$tableName'");
        if ($result->rowCount() > 0) {
            $conn->exec("DROP TABLE $tableName");
        } else {
            die(json_encode(array("success" => false, "error" => "Table does not exist")));
            http_response_code(400);
        }
    } catch(PDOException $e) {
        die(json_encode(array("success" => false, "error" => "Error checking or dropping table: " . $e->getMessage())));
        http_response_code(500);
    }

    echo json_encode(array("success" => true));
    $conn = null;
?>