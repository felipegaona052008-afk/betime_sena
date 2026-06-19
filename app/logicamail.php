<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require '../vendor/autoload.php';
require '../config/setting.php';
require '../database/Conexion.php';

if (isset($_POST['send'])):
  if (!empty($_POST['email'])) {
    $Usuario =ConsultaUsuarioPorEmail($_POST['email']); 
    if (count($Usuario) > 0) {
      echo "hemos enviado un correo";

      $token_ = bin2hex(random_bytes(32));

      if(updateUser($token_, TIEMPO_VIDA,$Usuario[0]->id_usuario));
      {
        EnviarCorreoResetPassword($Usuario[0]->email, $Usuario[0]->name, $Usuario[0]->id_usuario, $token_);
      }

      

    } else {
      // echo "no existe el usuario";
      $_SESSION['response'] = 'No existe usuario';
      header("location:../view/reset_password.php?message=no_found");
    }
  } else {
    // echo "Ingrese su correo electrónico";
    $_SESSION['response'] = 'Email incorrecto';
    header("location:../view/reset_password.php?message=error");
  }

  // header("location:../view/reset_password.php");
  header("location:../view/login.php?message=ok");

endif;



if (isset($_POST['save'])):
  if (!empty($_POST['id'])) {


    $new_password = password_hash($_POST['password'],PASSWORD_BCRYPT);
    $id = $_POST['id'];
    

    $Usuario =ConsultaUsuarioPorId($_POST['id']); 
    if (count($Usuario) > 0) {

      updateUserID($new_password,$id);

    } else {
      // echo "no existe el usuario";
      $_SESSION['response'] = 'No existe usuario';
    }
  } else {
    // echo "Ingrese su correo electrónico";
    $_SESSION['response'] = 'Email incorrecto';
  }

  header("location:../view/reset_password.php?message=success_password");

endif;




//metodo que consulta usuario por email
function ConsultaUsuarioPorEmail($email)
{

  $conex = new Conexion;

  $conex->sql = "SELECT * FROM usuarios WHERE email=:email";

  try {
    //code...
    $conex->pps = $conex->getConnection()->prepare($conex->sql);
    $conex->pps->bindParam(":email", $email);
    $conex->pps->execute();

    return $conex->pps->fetchAll(PDO::FETCH_OBJ);
  } catch (\Throwable $th) {
    //throw $th;

    echo $th->getMessage();
  } finally {
    $conex->closeDataBase();
  }
}



//metodo que consulta usuario por id
function ConsultaUsuarioPorId($id)
{

  $conex = new Conexion;

  $conex->sql = "SELECT * FROM usuarios WHERE id_usuario=:id";


  try {
    //code...
    $conex->pps = $conex->getConnection()->prepare($conex->sql);
    $conex->pps->bindParam(":id", $id);
    $conex->pps->execute();

    return $conex->pps->fetchAll(PDO::FETCH_OBJ);
  } catch (\Throwable $th) {
    //throw $th;

    echo $th->getMessage();
  } finally {
    $conex->closeDataBase();
  }
}


// actualizar usuario
function updateUser($token,$tiempo_vida,$user_id)
{
  $conex = new Conexion();
  $Valor = "1";

  $conex->sql = "UPDATE usuarios set request_password=:request_password, token_password=:token_password, expired_session=:expired_session WHERE id_usuario=:id_usuario";

  try {
    $conex->pps = $conex->getConnection()->prepare($conex->sql);
    $conex->pps->bindParam(":request_password", $Valor);
    $conex->pps->bindParam(":token_password",$token);
    $conex->pps->bindParam(":expired_session",$tiempo_vida);
    $conex->pps->bindParam(":id_usuario",$user_id);
    

    $conex->pps->execute();

  } catch (\Throwable $th) {
    //throw $th;
    echo $th->getMessage();
  }
}


// actualizar usuario
function updateUserID($new_password,$user_id)
{
  $conex = new Conexion();

  $conex->sql = "UPDATE usuarios set password=:password WHERE id_usuario=:id_usuario";


  try {
    $conex->pps = $conex->getConnection()->prepare($conex->sql);
    $conex->pps->bindParam(":password", $new_password);
    $conex->pps->bindParam(":id_usuario",$user_id);

    $conex->pps->execute();

  } catch (\Throwable $th) {
    //throw $th;
    echo $th->getMessage();
  }
}

//envio de correos electronicos
function EnviarCorreoResetPassword($Correo, $NombreReceptor,$userid,$token_User)
{

  //Create an instance; passing true enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;        //Enable verbose debug output
    $mail->isSMTP();                              //Send using SMTP
    $mail->Host       = HOST;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                     //Enable SMTP authentication
    $mail->Username   = USERNAME;                 //SMTP username
    $mail->Password   = PASSWORD;                 //SMTP password
    $mail->SMTPSecure = 'tls';                    //Enable implicit TLS encryption
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('betimesena@gmail.com', 'BeTimeSENA');
    $mail->addAddress($Correo, $NombreReceptor);     //Add a recipient

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Reseteo de password';
    $mail->Body    = 'Usted a solicitado un reseteo de contraseña <b> <a href="http://localhost/betime_sena/app/views/auth/nueva_password.phpid='.$userid.'&&token='.$token_User.'">Cambiar Contraseña</a> </b>';

    $mail->send();
    echo 'Se ha enviado un correo con las instrucciones para recuperar contraseña';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

}