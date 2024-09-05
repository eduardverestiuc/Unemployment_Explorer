<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Documentatia Proiectului Unx - Unemployment Explorer",
 *         description="Documentație pentru API-ul proiectului Unx - Unemployment Explorer",
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8081/PROIECT_TEHNOLOGII_WEB/FisierePHP",
 *         description="Server de Dezvoltare unde se ruleaza API-ul nostru cu fișiere PHP"
 *     )
 * )
 */

/**
 * @OA\Post(
 *     path="/admin_creaza_tabela.php",
 *     summary="Crează un tabel în baza de date și îl populează cu date dintr-un fișier CSV, in partea de admin",
 * security={{"jwt":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example=null
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="Eroare",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Mesaj de eroare"
 *             )
 *         )
 *     ),
 *     @OA\RequestBody(
 *         description="Numele tabelului și fișierul CSV pentru crearea tabelului",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="table-name",
 *                     description="Numele tabelului de creat",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="csv-file",
 *                     description="Fișierul CSV pentru crearea tabelului",
 *                     type="string",
 *                     format="binary",
 *                 )
 *             )
 *         )
 *     ),
 *     tags={"Admin - Creare Tabel (CSV)"}
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
    }

    $tableName = $_POST['table-name'];
    $csvFile = $_FILES['csv-file']['tmp_name'];

    if (!isset($tableName) || !isset($csvFile)) {
        die(json_encode(array("success" => false, "error" => "Invalid input data")));
    }

    try {
        $result = $conn->query("SHOW TABLES LIKE '$tableName'");
        if ($result->rowCount() > 0) {
            $conn->exec("DROP TABLE $tableName");
        }
    } catch(PDOException $e) {
        die(json_encode(array("success" => false, "error" => "Error checking or dropping table: " . $e->getMessage())));
    }

    try {
        $file = fopen($csvFile, 'r');
    } catch (Exception $e) {
        die(json_encode(array("success" => false, "error" => "Error opening file: " . $e->getMessage())));
    }

    $columns = fgetcsv($file);
    $columns = array_map('trim', $columns);

    try {
        $sql = "CREATE TABLE $tableName (";
        foreach ($columns as $column) {
            $sql .= "`$column` VARCHAR(50), ";
        }
        $sql = rtrim($sql, ', ') . ')';

        $conn->exec($sql);
    } catch(PDOException $e) {
        die(json_encode(array("success" => false, "error" => "Error creating table: " . $e->getMessage())));
    }

    try {
        $sql = "INSERT INTO $tableName (`" . implode('`, `', $columns) . "`) VALUES (" . str_repeat('?, ', count($columns) - 1) . '?)';
        $stmt = $conn->prepare($sql);

        while (($data = fgetcsv($file)) !== FALSE) {
            $data = array_map('trim', $data);
            $stmt->execute($data);
        }
    } catch(PDOException $e) {
        die(json_encode(array("success" => false, "error" => "Error inserting data: " . $e->getMessage())));
    }

    fclose($file);
    echo json_encode(array("success" => true));
    $stmt = null;
    $conn = null;
?>