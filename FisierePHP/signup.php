<?php
include('config.php');
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
session_start();
/**
 * @OA\Post(
 *     path="/signup.php",
 *     summary="Înregistrează un nou utilizator",
 *     tags={"Inregistrare Utilizatori"},
 *     @OA\RequestBody(
 *         description="Informații necesare pentru înregistrarea unui nou utilizator",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"username","password","phone","email"},
 *             @OA\Property(
 *                 property="username",
 *                 type="string",
 *                 description="Numele de utilizator ales"
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 description="Parola aleasă"
 *             ),
 *             @OA\Property(
 *                 property="phone",
 *                 type="string",
 *                 description="Numărul de telefon al utilizatorului"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="Adresa de email a utilizatorului"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilizator înregistrat cu succes sau existent",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Mesajul de răspuns - 'Utilizator înregistrat cu succes' sau 'Utilizatorul există deja'"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Date de intrare invalide",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Eroare de server intern",
 *     )
 * )
 */
$dbhost = 'localhost';
$dbname = 'proiect_tehnologii_web';
$dbuser = 'root';
$dbpass = 'root';

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$phone = $_POST['phone'];
$email = $_POST['email'];

try {
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $check = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email OR phoneNumber = :phone");
    $check->execute([':username' => $username, ':email' => $email, ':phone' => $phone]);

    if($check->rowCount() > 0){
        echo 'exists';
    }else{
        $code = rand(100000, 999999);
    
        $_SESSION['temp_user'] = [
            'username' => $username,
            'password' => $password,
            'phone' => $phone,
            'email' => $email,
            'code' => $code
        ];
    
        $mail = new \SendGrid\Mail\Mail();
        $mail->setFrom("pauleusebiubejan2003@gmail.com", "UnX-Unemployment Explorer");
        $mail->setTemplateId('d-88659710db08486caeea3c0d62eff230');
        $mail->addTo($_SESSION['temp_user']['email'], "Example User");
        $mail->addDynamicTemplateData('name', $username);
        $mail->addDynamicTemplateData('code', $code);

        $sendgrid = new \SendGrid(SENDGRID_API_KEY); 
        try {
            $response = $sendgrid->send($mail);
            if ($response->statusCode() != 202) {
                echo 'Eroare la trimiterea e-mailului: ' . $response->body();
            } else {
                echo 'verify';
            }
        } catch (Exception $e) {
            echo 'Eroare la trimiterea e-mailului: ' . $e->getMessage();
        }
    }
} catch(PDOException $e) {
    echo "Conectare esuata: " . $e->getMessage();
}
?>