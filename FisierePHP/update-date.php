<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
/**
 * @OA\Put(
 *     path="/update-date.php",
 *     summary="Actualizează datele utilizatorului",
 *     @OA\Response(
 *         response=200,
 *         description="Succes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="success"
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
 *                 example="exists"
 *             )
 *         )
 *     ),
 *     @OA\RequestBody(
 *         description="Datele utilizatorului pentru actualizare",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="emailUpdate",
 *                     description="Email-ul actual al utilizatorului pentru identificare",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     description="Noul email al utilizatorului",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="phoneNumber",
 *                     description="Noul număr de telefon al utilizatorului",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="username",
 *                     description="Noul nume de utilizator",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     description="Noua parolă a utilizatorului",
 *                     type="string",
 *                 )
 *             )
 *         )
 *     ),
 *     tags={"Utilizatori - Actualizare Date"}
 * )
 */
$host = 'localhost';
$dbname = 'proiect_tehnologii_web';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    $emailUpdate = $data['emailUpdate'];
    $email = $data['email'];
    $phoneNumber = $data['phoneNumber'];
    $username = $data['username'];
    $password = $data['password'];

    if ($password === "admin" || $username === "admin") {
        echo json_encode(["message" => "exists"]);
        return;
    }

    $checkSql = "SELECT * FROM users WHERE (email = ? OR phoneNumber = ? OR username = ?) AND email != ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$email, $phoneNumber,$username, $emailUpdate]);

    if ($checkStmt->rowCount() > 0) {
        echo json_encode(["message" => "exists"]);
    } else {
        if ($password !== "********") {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET email=?, phoneNumber=?, username=?, password=? WHERE email=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email, $phoneNumber, $username, $passwordHash, $emailUpdate]);
        } else {
            $sql = "UPDATE users SET email=?, phoneNumber=?, username=? WHERE email=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email, $phoneNumber, $username, $emailUpdate]);
        }

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "success"]);
        } else {
            echo json_encode(["message" => "incorrect"]);
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>