<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

/**
 * @OA\Post(
 *     path="/cartografie.php",
 *     summary="Trimite informații cartografice",
 *     tags={"Statistici Cartografice"},
 *     security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="Detalii necesare pentru trimiterea informațiilor cartografice",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="date",
 *                 type="string",
 *                 description="Datele necesare pentru trimiterea informațiilor cartografice"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Statistici cartografice trimise cu succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Informații cartografice trimise cu succes"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Acces neautorizat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Acces neautorizat din cauza lipsei tokenului JWT sau a tokenului invalid"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Acces restricționat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Acces restricționat. Necesită rolul de user."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Eroare de server intern",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Eroare internă a serverului"
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
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');
$json = file_get_contents('php://input');
$data = json_decode($json, true);
error_log(print_r($data, true));
$judete = $data['judete'];
$luni = $data['luni'];
$criterii = $data['criterii'];
$index = $data['index_luna'];
$lunaSelectata = $luni[$index];

$coordenates = [
    'ALBA' => ['lat' => 46.0669, 'lng' => 23.5719],
    'ARAD' => ['lat' => 46.1866, 'lng' => 21.3123],
    'ARGES' => ['lat' => 44.8606, 'lng' => 24.8679],
    'BACAU' => ['lat' => 46.5672, 'lng' => 26.9136],
    'BIHOR' => ['lat' => 47.0465, 'lng' => 21.9189],
    'BISTRITA NASAUD' => ['lat' => 47.1354, 'lng' => 24.4748],
    'BOTOSANI' => ['lat' => 47.7486, 'lng' => 26.6695],
    'BRASOV' => ['lat' => 45.6580, 'lng' => 25.6012],
    'BRAILA' => ['lat' => 45.2692, 'lng' => 27.9575],
    'BUZAU' => ['lat' => 45.1481, 'lng' => 26.8237],
    'CARAS-SEVERIN' => ['lat' => 45.3008, 'lng' => 21.8892],
    'CALARASI' => ['lat' => 44.2068, 'lng' => 27.3259],
    'CLUJ' => ['lat' => 46.7712, 'lng' => 23.6236],
    'CONSTANTA' => ['lat' => 44.1598, 'lng' => 28.6348],
    'COVASNA' => ['lat' => 45.8683, 'lng' => 25.7932],
    'DAMBOVITA' => ['lat' => 44.9333, 'lng' => 25.4500],
    'DOLJ' => ['lat' => 44.3302, 'lng' => 23.7949],
    'GALATI' => ['lat' => 45.4350, 'lng' => 28.0074],
    'GIURGIU' => ['lat' => 43.9037, 'lng' => 25.9699],
    'GORJ' => ['lat' => 45.0382, 'lng' => 23.2746],
    'HARGHITA' => ['lat' => 46.3606, 'lng' => 25.5247],
    'HUNEDOARA' => ['lat' => 45.8833, 'lng' => 22.9167],
    'IASI' => ['lat' => 47.1585, 'lng' => 27.6014],
    'IALOMITA' => ['lat' => 44.5642, 'lng' => 27.3616],
    'MARAMURES' => ['lat' => 47.6584, 'lng' => 23.58],
    'MUN. BUCURESTI' => ['lat' => 44.4268, 'lng' => 26.1025],
    'ILFOV' => ['lat' => 44.5582, 'lng' => 25.9592],
    'VRANCEA' => ['lat' => 45.6960, 'lng' => 27.1865],
    'MEHEDINTI' => ['lat' => 44.6319, 'lng' => 22.6561],
    'MURES' => ['lat' => 46.5456, 'lng' => 24.5624],
    'NEAMT' => ['lat' => 46.9312, 'lng' => 26.3709],
    'OLT' => ['lat' => 44.4304, 'lng' => 24.3714],
    'PRAHOVA' => ['lat' => 44.9493, 'lng' => 26.0365],
    'SATU MARE' => ['lat' => 47.7919, 'lng' => 22.8853],
    'SALAJ' => ['lat' => 47.1866, 'lng' => 23.0635],
    'SIBIU' => ['lat' => 45.7983, 'lng' => 24.1256],
    'SUCEAVA' => ['lat' => 47.6514, 'lng' => 26.2555],
    'TELEORMAN' => ['lat' => 43.9716, 'lng' => 25.3356],
    'TIMIS' => ['lat' => 45.7489, 'lng' => 21.2087],
    'TULCEA' => ['lat' => 45.1796, 'lng' => 28.8069],
    'VASLUI' => ['lat' => 46.6407, 'lng' => 27.7276],
    'VALCEA' => ['lat' => 45.0997, 'lng' => 24.3693],
];

$response = [];

try {
    foreach ($judete as $judet) {
        $stmt = $conn->prepare("SELECT `" . $criterii[0] . "` FROM " . $lunaSelectata . " WHERE judet = ?");
        $stmt->bindParam(1, $judet);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $resultsArray[$judet] = $result;
            $response[] = [
                'judet' => $judet,
                'criteriu' => $criterii[0],
                'valoare' => $result[$criterii[0]],
                'lat' => $coordenates[$judet]['lat'], 
                'lng' => $coordenates[$judet]['lng'],
                'luna' => $lunaSelectata    
            ];
        }
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    error_log($e->getMessage());
    exit;
}

$numericValues = array_map(function($item) use ($criterii) {
    return $item[$criterii[0]]; 
}, $resultsArray);

$minVal = min($numericValues);
$maxVal = max($numericValues);
$minSize = 1300;
$maxSize = 2000; 
$constProp = count($judete) > 1 && count($judete) < 5 ? 10 : 20;

foreach ($response as &$item) {
    if ($maxVal - $minVal == 0) {
        $proportion = 1;
    } else {
        $proportion = ($item['valoare'] - $minVal) / ($maxVal - $minVal) * $constProp;
    }
    $circleSize = $minSize + ($maxSize - $minSize) * $proportion;
    $item['raza'] = $circleSize; 
}

echo json_encode($response);
?>