<?php
require_once('../vendor/autoload.php');
require_once('../Libraries/jpgraph/src/jpgraph.php');
require_once('../Libraries/jpgraph/src/jpgraph_bar.php');
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/statistici_pdf.php",
 *     summary="Generează statistici sub formă de PDF",
 *     tags={"Statistici PDF"},
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
 *         description="Statistici generate și returnate sub formă de PDF",
 *         @OA\MediaType(
 *             mediaType="application/pdf",
 *             @OA\Schema(
 *                 type="string",
 *                 format="binary",
 *                 description="Documentul PDF generat"
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

// Crearea unui nou document PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Setare detalii document
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Numele tau');
$pdf->SetTitle('Statistici');

$pdf->SetFont('helvetica', '', 10);

// Conectare la baza de date
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obținere date din JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$judete = $data['judete'];
$luni = $data['luni'];
$criterii = $data['criterii'];
$index = $data['index_luna'];

$pdf->AddPage();

// Salvam criteriile pentru axele x si y 
$datayByCriteriu = [];
$dataxByCriteriu = [];

foreach ($judete as $judet) {
    foreach ($criterii as $index_criteriu => $criteriu) {
        if (!isset($datayByCriteriu[$criteriu])) {
            $datayByCriteriu[$criteriu] = [];
            $dataxByCriteriu[$criteriu] = [];
        }

        $stmt = $conn->prepare("SELECT `" . $criteriu . "` FROM ". $luni[$index] ." WHERE judet = ?");
        $stmt->execute([$judet]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $datayByCriteriu[$criteriu][] = $row[$criteriu];
            $dataxByCriteriu[$criteriu][] = $judet;
        }
    }
}

$criteriuColors = [
    '#FF5733',
    '#FFD700', 
    '#1E90FF',
];

$barPlots = [];
foreach ($criterii as $index_criteriu => $criteriu) {
    $bplot = new BarPlot($datayByCriteriu[$criteriu]);
    $bplot->SetFillColor($criteriuColors[$index_criteriu % count($criteriuColors)]);
    $barPlots[] = $bplot;
}

// Creare grafic
$graph = new Graph(900,600,'auto');
$graph->graph_theme = null;  
$graph->SetScale("textlin");


$graph->SetBox(false);

$graph->SetMargin(50,50,20,40);

$graph->ygrid->SetFill(false);
$graph->ygrid->Show(false);
$graph->xaxis->SetTickLabels(array_merge(...array_values($dataxByCriteriu)));
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$gbarplot = new GroupBarPlot($barPlots);
$graph->Add($gbarplot);

$graph->title->Set($luni[$index]);

$graph->Stroke(_IMG_HANDLER);

$fileName = uniqid() . ".png";
$graph->img->Stream($fileName);

$pdf->Image($fileName, '', '', 278, 158);

$x = 20; 
$y = 175;

foreach ($criterii as $index_criteriu => $criteriu) {
    list($r, $g, $b) = sscanf($criteriuColors[$index_criteriu % count($criteriuColors)], "#%02x%02x%02x");
    $pdf->SetTextColor($r, $g, $b);
    $pdf->Text($x, $y, $criteriu);
    $y += 5;
}

// Stergem fisierul creat temporar
if (file_exists($fileName)) {
    unlink($fileName);
}

// Închidere și output PDF
$pdf->Output('statistici.pdf', 'I');
?>