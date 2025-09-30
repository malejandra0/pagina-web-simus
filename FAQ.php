<?php
$servidor    = "localhost";
$usuario     = "root";
$clave       = "";
$baseDeDatos = "prueba1";

// conexion_bd
$con = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

//  envio del formulario 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mail       = $_POST['mail'];
    $comentario = $_POST['comentario'];

    // pa meter el comentario en la bd
    $sql = "INSERT INTO comentarios (mail, comentario) VALUES (?, ?)";
    $estado = $con->prepare($sql);
    $estado->bind_param("ss", $mail, $comentario);

    if ($estado->execute()) {
        echo "<script>alert('Comentario enviado con éxito.'); window.location.href='FAQ.html';</script>";
    } else {
        echo "<script>alert('Error al enviar el comentario. Inténtalo de nuevo.'); window.location.href='FAQ.html';</script>";
    }

    $estado->close();
}
$con->close();
?>  

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preguntas Frecuentes - SIMUS.MJN</title>
  <style>
    body {
      font-family: monospace, sans-serif;
      margin: 0;
      padding: 0;
      background: #f5f5f5;
      color: #333;
    }

    header {
      background: #6d5fe0;
      padding: 20px;
      text-align: center;
      color: white;
    }

    .faq-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .faq-container h2 {
      color: #6d5fe0;
      margin-bottom: 15px;
      text-align: left;
    }
    .faq-container p {
      text-align: justify;
      font-size: 16px;
      margin-bottom: 25px;
    }
    .faq-item {
      margin-bottom: 20px;
    }
    .faq-item h3 {
      color: #6d5fe0;
      margin-bottom: 8px;
    }
    .faq-item p {
      margin: 0;
      text-align: justify;
    }
    /* Estilos del formulario de comentarios */
    .comment-section {
      margin-top: 40px;
      padding: 20px;
      border-top: 2px solid #eee;
    }
    .comment-section h3 {
      color: #6d5fe0;
      margin-bottom: 15px;
      text-align: left;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    input, textarea {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
      width: 100%;
    }
    textarea {
      resize: vertical;
      min-height: 100px;
    }

    button {
      background: #6d5fe0;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 16px;
    }

    button:hover {
      background: #6d5fe0;
    }

    footer {
      text-align: center;
      padding: 20px;
      margin-top: 40px;
      background: #f0f0f0;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <header>
    <h1>SIMUS.MJN</h1>
    <h3>Preguntas Frecuentes</h3>
  </header>

  <section class="faq-container">
    <h2>¿Tienes dudas o recomendaciones?</h2>
    <p>
      Aquí encontrarás respuestas a las preguntas más comunes sobre nuestro software.  
      Si no encuentras lo que buscas, no dudes en enviarnos tus recomendaciones o inquietudes.  
      Tu opinión cuenta.
    </p>

    <div class="faq-item">
      <h3>¿Cómo descargo la versión gratuita?</h3>
      <p>En la sección de inicio encontrarás el botón "Versión gratuita". Al hacer clic, serás dirigido a la página de descarga.</p>
    </div>

    <div class="faq-item">
      <h3>¿Cómo puedo acceder a la versión completa?</h3>
      <p>Podrás descargar la versión completa desde la sección dedicada en la página web.</p>
    </div>

    <div class="faq-item">
      <h3>¿Dónde puedo enviar mis dudas o recomendaciones?</h3>
      <p>Puedes contactarnos a través de los correos proporcionados en el apartado de nosotros.</p>
    </div>

    <!-- comentarios -->
    <div class="comment-section">
      <h3>Comenta sugerencias o preguntas que tengas</h3>
      <form action="#.php" method="POST"> 
      <!--desde aqui guardar lo de base dde datos de los comentarios-->
        <input type="email" name="mail" placeholder="Tu correo electrónico" required>
        <textarea name="comentario" placeholder="Escribe aquí tu duda o recomendación..." required></textarea>
        <button type="submit">Enviar comentario</button>
      </form>
    </div>

  </section>

  <footer>
    <p>&copy; 2025 SIMUS.MJN - Todos los derechos reservados</p>
  </footer>

</body>
</html>
