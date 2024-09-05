<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/export_csv.php",
 *     summary="Exportă datele specificate în format CSV",
 *     tags={"Export CSV"},
 *     security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="Detaliile pentru exportul datelor în CSV",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="tables",
 *                 type="string",
 *                 description="Datele sub formă de tabel HTML pentru exportul în CSV"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Date exportate cu succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="csv",
 *                 type="string",
 *                 description="Datele exportate în format CSV"
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
 *         response=401,
 *         description="Acces neautorizat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de eșec a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de eroare pentru acces neautorizat"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Acces restricționat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de eșec a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de eroare pentru acces restricționat"
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

      if (isset($decoded->role) && $decoded->role === 'user') {
    } else {
        http_response_code(403);
        echo json_encode(array("message" => "Acces restricționat. Necesită rolul de user."));
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data) {
        $html = $data['tables'];
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $rows = $dom->getElementsByTagName('tr');

        $csvFile = fopen('php://memory', 'r+');

        foreach ($rows as $row) {
            $cols = $row->getElementsByTagName('td');
            $headers = $row->getElementsByTagName('th');
            $csvRow = [];
            foreach ($headers as $header) {
                $csvRow[] = $header->nodeValue;
            }
            foreach ($cols as $col) {       
                $csvRow[] = $col->nodeValue;
            }
            fputcsv($csvFile, $csvRow);
        }

        rewind($csvFile);
        $csv = stream_get_contents($csvFile);
        fclose($csvFile);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');

        echo $csv;
    } else {
        http_response_code(400);
        echo 'No data provided';
    }
} else {
    http_response_code(405);
    echo 'Invalid request method';
}
?>