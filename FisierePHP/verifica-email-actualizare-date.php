<?php
include('config.php');
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
header('Content-Type: application/json');
/**
 * @OA\Post(
 *     path="/verifica-email-actualizare-date.php",
 *     summary="Verifică email-ul utilizatorului și trimite un cod de verificare",
 *     @OA\Response(
 *         response=200,
 *         description="Succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="success"
 *             ),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phoneNumber",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="username",
 *                     type="string"
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="code",
 *                 type="integer",
 *                 example=123456
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="Eroare",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="incorrect"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Detalii suplimentare despre eroare, dacă este disponibil"
 *             )
 *         )
 *     ),
 *     @OA\RequestBody(
 *         description="Email-ul utilizatorului pentru verificare",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="email",
 *                     description="Email-ul utilizatorului pentru verificare",
 *                     type="string",
 *                 )
 *             )
 *         )
 *     ),
 *     tags={"Utilizatori - Verificare Email"}
 * )
 */

$servername = "localhost";
$dbname = "proiect_tehnologii_web";
$username = "root";
$password = "root";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = $_POST['code'];

    $sql = "SELECT email, phoneNumber, username FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    $stmt->execute([$email]);
    $result = $stmt->fetch();
    if ($result) {
        $user = [
            'email' => $result['email'],
            'phoneNumber' => $result['phoneNumber'],
            'username' => $result['username'],
        ];
        $randomCode = mt_rand(100000, 999999);
        $mail = new \SendGrid\Mail\Mail();
        $mail->setFrom("pauleusebiubejan2003@gmail.com", "UnX-Unemployment Explorer");
        $mail->setTemplateId('d-1839a135a339461a8553cc904da6b7cf');
        $mail->addTo($email, "Example User");
        $mail->addDynamicTemplateData('name', $user['username']);
        $mail->addDynamicTemplateData('code', $randomCode);

        $sendgrid = new \SendGrid(SENDGRID_API_KEY);
        try {
            $response = $sendgrid->send($mail);
            if ($response->statusCode() != 202) {
                echo json_encode(["message" => "incorrect"]);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(["message" => "incorrect", "error" => $e->getMessage()]);
            exit;
        }
        echo json_encode(["message" => "success", "user" => $user, "code" => $randomCode]);
    } else {
        echo json_encode(["message" => "incorrect"]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["message" => "incorrect", "error" => $e->getMessage()]);
    exit;
}
