<?php
include('config.php');
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
use \Firebase\JWT\JWT;
/**
 * @OA\Post(
 *     path="/login.php",
 *     summary="Autentifică utilizatorii și returnează un token JWT",
 *     tags={"Autentificare"},
 *     @OA\RequestBody(
 *         description="Numele de utilizator și parola pentru autentificare",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"username","password"},
 *             @OA\Property(
 *                 property="username",
 *                 type="string",
 *                 description="Numele de utilizator"
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 description="Parola"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Autentificare reușită",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a autentificării"
 *             ),
 *             @OA\Property(
 *                 property="admin",
 *                 type="boolean",
 *                 description="True dacă utilizatorul este admin"
 *             ),
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 description="Tokenul JWT generat pentru sesiune"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Autentificare eșuată",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Starea de succes a autentificării, false pentru eșec"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de eroare"
 *             )
 *         )
 *     )
 * )
 */
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
    $password = $data->password;

    if ($username === 'admin' && $password === 'admin') {
        $payload = array(
            "user" => $username,
            "role" => "admin",
        );
        $jwt = JWT::encode($payload, 'secret_key', 'HS256');
        echo json_encode(array("success" => true, "admin" => true, "token" => $jwt));
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if (password_verify($password, $result['password'])) {
                $payload = array(
                    "user" => $username,
                    "role" => "user",
                );
                $jwt = JWT::encode($payload, 'secret_key', 'HS256');
                echo json_encode(array("success" => true, "token" => $jwt));
            } else {
                echo json_encode(array("success" => false, "error" => "Invalid"));
                http_response_code(401);
            }
        } else {
            echo json_encode(array("success" => false, "error" => "User not found"));
            http_response_code(401);
        }
    }
} catch(PDOException $e) {
    echo json_encode(array("success" => false, "error" => $e->getMessage()));
    http_response_code(500);
}

$stmt = null;
$conn = null;