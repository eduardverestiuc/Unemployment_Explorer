<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

/**
 * @OA\Post(
 *     path="/statistici_svg.php",
 *     summary="Generează statistici sub formă de SVG",
 *     tags={"Statistici SVG"},
 *     security={{"jwt":{}}},
 *     @OA\RequestBody(
 *         description="Detalii necesare pentru generarea statisticii",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="date",
 *                 type="string",
 *                 description="Datele necesare generării statisticii"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Statistici generate și returnate sub formă de SVG",
 *         @OA\MediaType(
 *             mediaType="image/svg+xml",
 *             @OA\Schema(
 *                 type="string",
 *                 format="binary",
 *                 description="Documentul SVG generat"
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

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $judete = $data['judete'];
    $luni = $data['luni'];
    $criterii = $data['criterii'];
    $index = $data['index_luna'];
    $lunaSelectata = $luni[$index];
    $resultsArray = [];
    foreach ($judete as $judet) {
        $stmt = $conn->prepare("SELECT `" . $criterii[0] . "` FROM " . $lunaSelectata . " WHERE judet = ?");
        $stmt->bindParam(1, $judet);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $resultsArray[$judet] = $result;
        }
    }

    if(count($judete) == 42)
    {
        $stmt = $conn->prepare("SELECT `" . $criterii[0] . "` FROM " . $lunaSelectata . " WHERE judet = 'ROMANIA'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $resultsArray["ROMANIA"] = $result;
    }

    $svgFile = '../Imagini/romania.svg';
    $svgContent = file_get_contents($svgFile);

    $dom = new DOMDocument();
    $dom->loadXML($svgContent);

    $circles = $dom->getElementsByTagName('circle');

    $numericValues = array_map(function($item) use ($criterii) {
        return $item[$criterii[0]]; 
    }, $resultsArray);

    $minVal = min($numericValues);
    $maxVal = max($numericValues);
    $minSize = 3;
    $maxSize = 22;
    if (count($judete) > 1 && count($judete) < 5){
        $constProp = 1;
       }
        else {
            $constProp = 10;
        }
    if($criterii[0] == "Rata somajului" || $criterii[0] == "Rata somajului feminina" || $criterii[0]== "Rata somajului masculina"){
        $constProp = 0.8;
    }
    foreach ($judete as $judet) {
        foreach ($circles as $circle) {
            if ($circle->getAttribute('class') === $judet) {
                if ($maxVal - $minVal == 0) {
                    $proportion = 1;
                } else {
                    $proportion = ($resultsArray[$judet][$criterii[0]] - $minVal) / ($maxVal - $minVal) * $constProp;
                }
                $circleSize = $minSize + ($maxSize - $minSize) * $proportion;
                $circle->setAttribute('r', $circleSize);
                $circle->setAttribute('fill', 'red');
                break; // pentru optimizare
            }
        }
    }

    $paths = $dom->getElementsByTagName('path');

    foreach ($paths as $path) {
        $path->setAttribute('class', 'hover-effect');
    }

    $styleElement = $dom->createElement('style', '.hover-effect:hover { fill: pink; }');
    $dom->getElementsByTagName('svg')->item(0)->appendChild($styleElement);

    $modifiedSvgContent = $dom->saveXML();

    // Setăm header-ul pentru HTML
    header('Content-Type: text/html');

    // Punem SVG-ul modificat în HTML
    echo '<!DOCTYPE html><html><head><style>
    .centered-content { 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        text-align: center; 
        height: 100vh;
    }
    svg { 
        margin: auto;
        display: block; 
        width: 100%; /* Face SVG-ul să ocupe întreaga lățime a părintelui său */
        height: auto; /* Permite SVG-ului să se redimensioneze proporțional */
        max-width: 100%; /* Limitează lățimea maximă la 100% */
    }
    @media screen and (max-width: 1200px) {
        .centered-content {
            height: auto;
            font-size: 12px;
        }
        svg {
            margin: 0;
        }
    }
    </style></head><body>';
    // Container pentru text și SVG
    echo '<div class="centered-content">'; 
    if (count($judete) == 42){
        echo "Judete primite: " . "Toate judetele" . "<br>";
        echo "Luna selectata: " . $lunaSelectata . "<br>";
        echo "Pentru ROMANIA, rezultatul este " . htmlspecialchars($resultsArray["ROMANIA"][$criterii[0]]) . "<br>";
    }
        else {
            echo "Judete primite: " . implode(", ", $judete) . "<br>";
            echo "Luna selectata: " . $lunaSelectata . "<br>";
            foreach ($resultsArray as $judet => $result) {
                echo "Pentru judetul " . htmlspecialchars($judet) . ", rezultatul este " . htmlspecialchars($result[$criterii[0]]) . "<br>";
            }  
        }  
    echo $modifiedSvgContent;
    echo '</div>';
    echo '</body></html>';
    
} catch(PDOException $e) {
    echo json_encode(array("success" => false, "error" => $e->getMessage()));
}

$conn = null;
?>