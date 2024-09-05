<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/statistici_csv.php",
 *     summary="Generează statistici sub formă de CSV pe baza criteriilor specificate",
 *     tags={"Statistici CSV"},
 *     security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="Detaliile pentru generarea statisticilor",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="judete",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="Lista de județe pentru care se generează statistici"
 *             ),
 *             @OA\Property(
 *                 property="luni",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="Lunile pentru care se generează statistici"
 *             ),
 *             @OA\Property(
 *                 property="criterii",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="Criteriile pe baza cărora se generează statistici"
 *             ),
 *             @OA\Property(
 *                 property="index_luna",
 *                 type="integer",
 *                 description="Indexul lunii pentru care se generează statistica"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Statistici generate cu succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="string",
 *                 description="Statistici generate sub formă de tabel HTML"
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
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proiect_tehnologii_web";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $judete = $data['judete'];
    $luni = $data['luni'];
    $criterii = $data['criterii'];
    $index = $data['index_luna'];

    $output = '<table style="border: 3px solid #324d62; border-radius: 10px; margin-top: 5%; color: #324d62; background: linear-gradient(to bottom, rgba(255, 255, 255, 0.7), rgba(240, 240, 240, 0.7));">';

    $output .= '<tr>';
    $output .= '<th style="text-align: center; font-size: 3vw; " colspan="' . (count($criterii) + 1) . '">' . htmlspecialchars($luni[$index]) . '</th>';
    $output .= '</tr>';

    $output .= '<tr>';
    $output .= '<th style="font-size: 2vw; font-weight: bold; ">Judet </th>';
    foreach ($criterii as $criteriu) {
        $output .= '<th style="font-size: 1.5vw; font-weight: bold; ">' . htmlspecialchars($criteriu) . '</th>';
    }
    $output .= '</tr>';

    for ($j = 0; $j < count($judete); $j++) {
        $output .= '<tr>';
        if($judete[$j] != "Niciun Judet")
        {
            $output .= '<td style="font-size: 1.5vw; font-weight: bold; ">' . htmlspecialchars($judete[$j]) . '</td>';
            for ($i = 0; $i < count($criterii); $i++) {
            $stmt = $conn->prepare("SELECT `" . $criterii[$i] . "` FROM ". $luni[$index] ." WHERE judet = ?");
            $stmt->bindParam(1, $judete[$j]);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $output .= '<td>' . htmlspecialchars($result[$criterii[$i]]) . '</td>';
            } else {
                $output .= '<td>N/A</td>';
            }
            }
        }

        $output .= '</tr>';
    }

    $output .= '</table>';

    echo $output;

} catch(PDOException $e) {
    echo json_encode(array("success" => false, "error" => $e->getMessage()));
}

$stmt = null;
$conn = null;