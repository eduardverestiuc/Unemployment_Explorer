<?php
include('config.php');
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
/**
 * @OA\Post(
 *     path="/verifyJWT.php",
 *     summary="Verifică JWT-ul trimis și extrage rolul utilizatorului",
 *     tags={"Verificare JWT"},
 *     @OA\RequestBody(
 *         description="JWT-ul trimis de utilizator pentru verificare",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"token"},
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 description="JWT-ul trimis de utilizator"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="JWT valid, rolul utilizatorului returnat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a operațiunii"
 *             ),
 *             @OA\Property(
 *                 property="role",
 *                 type="string",
 *                 description="Rolul utilizatorului extras din JWT"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Eroare la decodarea JWT-ului sau JWT invalid",
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
$json = file_get_contents('php://input');
$data = json_decode($json);

$jwt = $data->token;
error_log("Tokenul este: " .$jwt);

try {
    $decoded = JWT::decode($jwt, new Key('secret_key', 'HS256'));
    error_log("Rolul este: " .$decoded->role);
    echo json_encode(array("success" => true, "role" => $decoded->role));
  } catch (Exception $e) {
    echo json_encode(array("success" => false, "error" => $e->getMessage()));
    http_response_code(500);
  }
?>