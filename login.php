<?php
$servidor    = "localhost";
$usuario     = "root";
$clave       = "";
$baseDeDatos = "prueba1";

// conexion_bd
$con = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if ($con->connect_error){
    die ("Error en la conexion con la base de datos ".$con->connect_error);
}

//ver que si se envio el formulario
if($_SERVER["REQUEST_METHOD"]=="POST"){
    //obtener datos pa manar
    $mail = trim($_POST['mail']);
    $contrasena = $_POST['contrasena'];

    //que no este vacio
    if (empty($mail) || empty($contrasena)){
        echo json_encode(['success' => false, 'error' => 'Completa todos los campos']);
        exit;
    }
    //se consulta la bd y se almacena en ?
    $bd = "SELECT mail, nombre, contrasena FROM usuarios WHERE mail = ?";
    $consulta = $con->prepare($bd);

    if ($consulta){
        $consulta->bind_param("s", $mail);
        $consulta->execute();
        $resultao = $consulta->get_result();

        //pa ver si el usuaruio esta
        if ($resultao->num_rows === 1){
            $usuario = $resultao->fetch_assoc();

            //contraseña verif
            if (password_verify($contrasena, $usuario['contrasena'])){
                // si esta se le hace un token
                $token = base64_encode($usuario['mail'] . ":" . $usuario['nombre']);
                
                // avisito de q si esta
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'nombre' => $usuario['nombre']
                ]);
                exit;
            } else{
                //contraseña incorrecta
                echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta']);
                exit;
            }
        } else {
            //no hay usuario
            echo json_encode(['success' => false, 'error' => 'No hay usuario']);
            exit;
        }
    } else{
        //error en la preparacion
        echo json_encode(['success' => false, 'error' => 'Error en el sistema']);
        exit;
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang ="es">
  <head>
    <meta charset="UTF-8">
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <style>
      body{
        font-family: monospace, sans-serif;
        margin:0;
        padding: 0;
        background:url(imgs/fondojuego.png);
        color: #333;
        position:relative;
        min-height: 100vh;
      }
      /*encabezado*/
      .encabezadodepagina {
        background:white;
        padding: 1rem 2rem; /*arriba y abajo 1rem y a los lados 2rem*/
        box-shadow:0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items:center;
        position: relative;
        top:0;
        left:0;
        z-index: 3;
      }

      .logo {
        font-size:24px;
        font-weight: bold;
        color:#e94a84;
      }
      .nav a{
        margin:0 15px;
        text-decoration: none;
        color:#333;
        font-weight: 500;
      }

      .nav a:hover {
        color: #d72868;
      }

      .contenedorprincipal {
        position: relative;
        height: 100vh;
        overflow: hidden;
      }
      /*caja de imagen del fondo que detras del login*/
      .contimagen{
        position: absolute; /*que se quede pegado detras delo gin*/
        top: 0; 
        left: 0; 
        width: 100%; /*ocupa todo el ancho*/
        height:100%;
        z-index:1; /*pa que quede detras del login*/
      }
      .contimagen img{
        width: 100%;
        height: 100%;
        object-fit:cover; /*la imagen no se dforme todo feo*/
        opacity: 0.7; /*asi no opaca el login*/
      }
      
      .overlay{
        position:absolute;
        top:0;
        left:0;
        width:100% ;
        height: 100%;
        background:linear-gradient(135deg, #6d5fe0, #8a7cf7);
        z-index:2;
        opacity:0.56;
      }

      /*el coso de contenedor del login*/
      .cajadelogin{ /*clase*/
        display:flex;  /*todo lo que va adentro se va acomodar en fila*/
        justify-content: flex-end; /*esto es pa que quede a la derecha */
        align-items: center; /*los cosos internos queden centrados*/
        height:100vh; /*espacio pra que se vea la imagen*/
        padding:0 70px 0 0;
        position:relative;
        z-index:3;
      }
      .cajitadeingreso{
        background: white;
        padding: 40px; /*pa que respire entre los bordes*/
        border-radius: 12px; /*redondea los borde de la caja*/
        box-shadow: 0 4px 15px rgba(0,0, 0, 0.2); /*el rgba es el color de la sombra3*/
        /* 0desplazamiento horizontal  4 en vertical y 15 de desenfoque*/
        width: 350px; /*ancho*/
        text-align: center; /*los textos van centrados*/
        z-index: 4; /*pa que quede por encima de l fondo*/
      }
      .cajitadeingreso h2{
        margin-bottom:20px; /*espacio entre el titulo y lo otro*/
        color: #6d5fe0;
      }

      .cajitadeingreso input {
        width: 100%; /*acupa todo el ancho del contenedor*/
        padding:12px; 
        margin: 10px 0; /*margen arriba y abajo*/
        border:1px solid #ccc;
        border-radius:8px;
        color:rgb(134, 82, 82);
        font-size : 16px; 
        box-sizing: border-box;
      }
      .cajitadeingreso button{
        width: 100%;
        padding:12px;
        background: #6d5fe0;
        color:white;
        border:none;
        border-radius: 8px;
        font-size:16 px;
        cursor: pointer;
        transition:background 0.3s ease;
        margin-top:10px;
      }
      .cajitadeingreso button:hover{
        background: #6151d5; /*color cuando se pase por encima con el mouse*/
      }
      .cajitadeingreso a{
        display:block;  /*acupa toda la linea*/
        margin-top: 15px; /*que el boton no quede todo pegado*/
        font-size:14px;
        text-decoration: none; /*sin subrayado*/;
        color: #6d5fe0;
      }
      .mensajeerror{
        color:#d93025;
        background-color: #fce8e6;
        padding:10px;
        border-radius: 4px;
        border:1px solid #f28b82;
        margin:10px 0;
        font-size: 14px;
        display:none;
      }
      .inputerror{
        border:2px solid #d93025 !important;
      }
      /*contenido de historia, propositos y caracteristicas*/
      .secciondecontenido{
        display:flex ;
        align-items: center;
        justify-content:center;
        padding:60px 20px;
        background: #fff;
        position: relative;
        z-index:3;
      }
      .secciondecontenido.dark {
        background: #f8f9fa;
      }

      .imagendelcontenedor{
        width: 350px ;
        height:250px;
        border-radius: 16px;
        margin-right:40px;
        overflow:hidden; /*estono se sale de los bordes*/
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      .imagendelcontenedor img {
        width:100%;
        height:100%;
        object-fit:cover;
        display:block; /*que nop haya espacio debajo*/
      }
      .contenidodetexto{
        max-width:500px;
      }
      .contenidodetexto h2{
        color:#6d5fe0;
        margin-bottom:15px;
        font-size:28px;
      }
      .contenidodetexto p {
        line-height:1.6; /*interlineado*/
        color:#281d1d;
        margin-bottom: 15px;
      }
      .contenidodetexto ul{
        color:#2e1f1f;
        line-height:1.6;
        padding-left:20px;
      }
      .contenidodetexto li{ /*cosito o puntode la lista*/
        margin-bottom: 8px;
      }

      /*pie o footer de pagina*/
      .piedepagina{
        background:#0b2a49;
        text-align:center;
        padding: 40px 20px;
        color:#166275;
        font-size:14px;
        position:relative;
        z-index: 3 ;
      }
      .contendidodelpie{
        max-width:1000px;
        margin: 0 auto; /*se centre horizontalmente*/
        display:flex;
        flex-wrap: wrap; /*no se salfa de la pantalla*/
        justify-content:space-around;
        text-align:left ;
        margin-bottom:30px;
      }
      .columnadelpie{ /*cada columna del footer*/
        flex:1;
        min-width: 250px;
        margin:15px;
      }
      .columnadelpie h3{
        color:#1b0e78;
        margin-bottom:20px;
        font-size:18px;
        border-bottom:2px solid #6d5fe0;
        padding-bottom:10px;
      }
      .columnadelpie a{
        display:block;
        color:#1b0e78;
        text-decoration:none;
        margin-bottom:10px; 
        transition:color 0.3s; 
      }
      .columnadelpie a:hover {
        color: #1b0e78;
      }

      .inferiorpie{
        margin-top:30px;
        padding-top:20px;
        border-top: 1px solid #06090b;
        text-align: center;
      }
      .espaciodeimagenfooter{
        width:100%;
        height:150px;
        border:2px dashed #c72828;
        margin:20px 0;
        display:  flex;
        justify-content:center;
        align-items: center;
        font-style: italic;
        color:#941b1b;
      }
    </style>
  </head>
  <body>
    <!--el encabezadp-->
    <div class="encabezadodepagina">
      <div class="logo">SIMUS.MJN</div>
      <div class ="nav">
        <a href="nosotros.html">Nosotros</a>
        <a href="index.php">Registro</a>
      </div>
    </div>
    <!--contenedor prin mas imaagen del software-->
    <div class="contenedorprincipal">



      <!-- aqui empieza lo del php-->
      <div class="cajadelogin">
        <div class="cajitadeingreso">
          <h2>Iniciar sesión</h2>
          <div id="mensajeerror" class="mensajeerror"></div>

          <form id="formulariologin" method="POST">
            <input type="email" name="mail" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
            <a href="index.php">¿No tienes cuenta? Regístrate</a>
          </form>
        </div>
      </div>
    </div>
<!--aqui acaba lo del php -->



    <!--la historia de cono creamos esto-->
    <seccion class="secciondecontenido">
      <div class="imagendelcontenedor">
        <img src="imgs/fondojuego.png" alt="fondo del juego(tengo que cambiar la imagen)">

      </div>
      <div class="contenidodetexto">
        <h2>Nuestra historia</h2>
        <p>
          SIMUS.MJN nace en la universidad, cuando en tercer semestre trabajamos en una página de física. Esa experiencia nos enseñó lo valioso que es apostar por la innovación, aunque a veces no sea reconocida como uno espera. Durante el mismo tiempo, una historia cercana nos inspiró profundamente: la experiencia de un niño llamado Antony, diagnosticado
          con PKAN, quien solo podía comunicarse a través de movimientos oculares. 
        </p>
        <p>
          Saber cómo su profesora lograba conectar con él, a pesar de esas limitaciones, nos marcó e impulso una solución tecnológica que pudiera mejorar la comunicación y la calidad de vida de personas en situaciones similares. Con esa motivación, durante las vacaciones empezamos a darle forma a una idea que más tarde se consolidaría en un proyecto real.
        </p>
      </div>
    </seccion>

    <!--propositos-->
    <seccion class="secciondecontenido dark">
      <div class="contenidodetexto">
        <h2>Nuestro proposito</h2>
        <p>
          En SIMUS.MJN, nuestro proposito es desarrollar un software inclusivo que empodere a los niños con discapacidades motrices, facilitando 
          su comunicación y entretenimiento educativo a través de interfaces innovadoras y accesibles.
        </p>
        <p>Nuestros objetivos principales:</p>
        <ul>
          <li> Desarrollar un software intuitivo que responda a movimientos y gestos oculares</li>
          <li> Suministrar una herramienta accesible para cuidadores, familiares, tutores, etc.</li>
          <li> Crear una experiencia educativa y comuncicativa de forma divertida para los niños con movilidad reducida</li>
        </ul>
      </div>
      <div class="imagendelcontenedor">
        <img src="imgs/fondojuego.png" alt ="fondo que tengo que cambiar x2">
      </div>
    </seccion>

    <seccion class="secciondecontenido">
      <div class="imagendelcontenedor">
        <img src="imgs/fondojuego.png" alt="fondo que toca cambiar x3">
      </div>
      <div class="contenidodetexto">
        <h2>Características</h2>
        <p>
          Nuestro software incorpora el diseño de interfaces accesibles para crear una experiencia única.
        </p>
        <ul>
          <li> Seguimiento facial con reconocimiento ocular para la interacción con el software</li>
          <li> Minijuegos educativos básicos, un ejemplo es el de "Animalia", donde el infante identifique el ambiente donde habita un cierto tipo de animal.</li>
          <li> Diseño colorido y atractivo para los niños</li>
          <li> Sección de interacción cuidador-niño con paneles de palabras dividido en secciones, el niño o niña podrá escoger según su necesidad a través del gesto ocular; el software producirá el audio con la palabra escogida.</li>
        </ul>
      </div>
    </seccion>
    <piedepagina>
      <div class ="contendidodelpie">
        <div class="columnadelpie">
          <h3>SIMUS.MJN</h3>
          <p> Software inclusivo que combina aprendizaje, comunicación y diversión.</p>
        </div>
        <div class="columnadelpie">
          <h3>Recursos</h3>
          <a href="https://youtu.be/DOo2jXZ3aWc?si=CEuu-p9G20gTNyor">Tutoriales</a>
        </div>
        <div class="columnadelpie">
          <h3>Legal</h3>
          <a href="terminos.html">Términos y condiciones de uso</a>
        </div>
      </div>
      <div class="inferiorpie">
        <p><strong>SIMUS.MJN</strong> - Software para educación inclusiva</p>
        <p>Copyright © 2025 SIMUS.MJN - Todos los derechos reservados</p> 
      </div>
    </piedepagina>

<script>
  document.addEventListener('DOMContentLoaded',function(){
    if (localStorage.getItem('token_sesion')) {
      window.location.href = 'pprin.php';
    }
    //se espera que este completamente cargado
    //obtiene los paramentros de la url 
    //el urlsearchparams obtiene los parametros
    const urlParams=new URLSearchParams(window.location.search);
    const error=urlParams.get('error'); //obtiene el valor error
    const mensajeerror=document.getElementById('mensajeerror');//busca en el html un id donde se muestran los mensajes
    if (error){ //si existe error en la url o el login
      mensajeerror.style.display='block'; //se muestra el mensaje de error que en un principo esta oculto
      switch(error){ //mira que error es para mostrar su respectivo mensaje
        case 'usuarioNoEncontrado':
          mensajeerror.textContent='Usuario no encontrado. Verifica tu correo.';
          break;
        case 'contrasenaIncorrecta':
          mensajeerror.textContent='Contraseña incorrecta, intenta nuevamente.';
          break;
        case 'camposVacios':
          mensajeerror.textContent='Completa todos los campos.';
          break;
        default:
          mensajeerror.textContent='Error al iniciar sesión.'
      }
    }
    //se toman encuenta los eventos al formulario con su id
    //se acitva cuanfo el usuario trate de enviar el formulario 
    document.getElementById('formulariologin').addEventListener('submit', function(e) { 
      //busca prevenir que el formulario se envie de una vez 
      e.preventDefault();
      const mail=document.querySelector('input[name="mail"]').value.trim();//obtiene el correo y elimina espacios al inicio y al final
      const contrasena=document.querySelector('input[name="contrasena"]').value;//lo mismo pero con la contraseña

      mensajeerror.style.display='none';//esconde cualquier mensaje de error antes
      document.querySelectorAll('input').forEach(input =>{ //hace un recorrido en todos los inputs del formulario y quita la clase de error
        input.classList.remove('inputerror');
    });

      let hayerror=false; //variable para controlar si hay errores
      if (mail==='' || contrasena===''){
        mensajeerror.textContent='Por favor, completa los campos';
        mensajeerror.style.display='block';
        hayerror=true 
      }
      if (mail !==''&& !/^\S+@\S+\.\S+$/.test(mail)){ ///^\S+@\S+\.\S+$/.test(mail) es una expresión regular que valida el formato del correo electrónico
        mensajeerror.textContent='El correo no es válido';
        mensajeerror.style.display='block';
        //se mete la clase de error para el input de mail
        document.querySelector('input[name="mail"]').classList.add('input-error');
        hayerror=true;
      }
      if (!hayerror){//si no hay errores en lo anterior
        const formdata= new FormData();
        formdata.append('mail', mail);
        formdata.append('contrasena', contrasena);
        fetch ('login.php', {
          method:'POST',
          body: formdata
        })  
        .then(response=> response.json())
        .then(data => {
          if (data.success) {
            // Login exitoso
              localStorage.setItem('token_sesion', data.token);
              localStorage.setItem('nombre', data.nombre);
              window.location.href = 'pprin.php';
          } else {
            // Mostrar mensaje de error
            mensajeerror.textContent = data.error;
            mensajeerror.style.display = 'block';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          mensajeerror.textContent = 'fallo del sistema';
          mensajeerror.style.display = 'block';
        });
      }
    });
      document.querySelectorAll('input').forEach(input => { //se toma en cuenta los eventos a cada input del formulario
      //se activs cada vez qwue el usuario escribe algo dentro de los campos
      input.addEventListener('input', function() { 
        mensajeerror.style.display = 'none';
        //quita la clase de error del input actual
        this.classList.remove('input-error');
      });
    });
  });
</script>

  </body>
</html>
