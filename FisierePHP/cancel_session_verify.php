<?php
require '../vendor/autoload.php';
use OpenApi\Annotations as OA;
/**
 * @OA\Get(
 *     path="/cancel_session_verify.php",
 *     summary="Anulează sesiunea de verificare a utilizatorului temporar și redirecționează către pagina de sign-up",
 *     tags={"Anuleaza Sesiunea de Verificare Temporara a Utilizatorului"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirecționează către pagina de sign-up",
 *         @OA\Header(
 *             header="Location",
 *             description="URL-ul paginii de sign-up",
 *             @OA\Schema(
 *                 type="string",
 *                 format="url"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Eroare de server intern",
 *     )
 * )
 */
session_start();
unset($_SESSION['temp_user']);
header("Location: /PROIECT_TEHNOLOGII_WEB/FisiereHTML/signup.html");
exit();
?>