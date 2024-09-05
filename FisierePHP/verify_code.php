<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
session_start();
/**
 * @OA\Post(
 *     path="/verify_code.php",
 *     summary="Verifică codul trimis de utilizator și finalizează înregistrarea",
 *     tags={"Verificare Cod"},
 *     @OA\RequestBody(
 *         description="Codul trimis de utilizator pentru verificare",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"code"},
 *             @OA\Property(
 *                 property="code",
 *                 type="string",
 *                 description="Codul trimis de utilizator"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cod corect și înregistrare finalizată",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de succes - 'success'"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Cod incorect sau lipsă",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de eroare - 'incorrect'"
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
 *                 description="Mesajul de eroare generat de server"
 *             )
 *         )
 *     )
 * )
 */
if (!isset($_POST['code'])) {
    echo 'incorrect';
    exit;
}

$code = $_POST['code'];

if (!isset($_SESSION['temp_user']['code'])) {
    echo 'incorrect';
    exit;
}

if ($code != $_SESSION['temp_user']['code']) {
    echo 'incorrect';
    exit;
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=proiect_tehnologii_web", 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $user = $_SESSION['temp_user'];
    $stmt = $conn->prepare("INSERT INTO users (username, password, phoneNumber, email) VALUES (:username, :password, :phone, :email)");
    $stmt->execute([':username' => $user['username'], ':password' => $user['password'], ':phone' => $user['phone'], ':email' => $user['email']]);
    unset($_SESSION['temp_user']);
    echo 'success';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

?>