<?php
  //start a session -- needed for Securimage Captcha check
  session_start();

  //add you e-mail address here
  define("MY_EMAIL", "info@radiadoresaznar.es");
  

  /**
   * Sets error header and json error message response.
   *
   * @param  String $messsage error message of response
   * @return void
   */
  function errorResponse ($messsage) {
    header('HTTP/1.1 500 Internal Server Error');
    die(json_encode(array('message' => $messsage)));
  }

  /**
   * Return a formatted message body of the form:
   * Name: <name of submitter>
   * Comment: <message/comment submitted by user>
   *
   * @param String $name     name of submitter
   * @param String $messsage message/comment submitted
   */
  function setMessageBody ($name, $email, $phone, $message) {
    $message_body = "Nombre: " . $name."\n\n";
    $message_body .= "Email: " . $email."\n\n";
    $message_body .= "Teléfono: " . $phone."\n\n";
    $message_body .= "Mensaje:\n" . nl2br($message);
    return $message_body;
  }

  $name = $_POST["name"];
  $email = (isset($_POST['email'])) ? $_POST['email'] : "N/A";
  $phone = (isset($_POST['phone'])) ? $_POST['phone'] : "N/A";
  $message = $_POST['message'];

  header('Content-type: application/json');
  //do some simple validation. this should have been validated on the client-side also
  
  if (empty($email) && empty($phone)) {
  	errorResponse('Debe introducir un teléfono o un email de contacto.');
  }
  
  if (empty($message)) {
  	errorResponse('El mensaje no puede estar vacío.');
  }

  //do Captcha check, make sure the submitter is not a robot:)...
  include_once './vender/securimage/securimage.php';
  $securimage = new Securimage();
  if (!$securimage->check($_POST['captcha_code'])) {
    errorResponse('Los caracteres de la imagen son inválidos');
  }

  //try to send the message
  if(mail(MY_EMAIL, "Contacto desde la web", setMessageBody($name, $email, $phone, $message), "From: $email")) {
  	echo json_encode(array('message' => 'Tu mensaje ha sido enviado. Apreciamos tu interés en contactarnos.'));
  } else {
  	header('HTTP/1.1 500 Internal Server Error');
  	echo json_encode(array('message' => 'Error inesperado a la hora de enviar el e-mail.'));
  }
?>
