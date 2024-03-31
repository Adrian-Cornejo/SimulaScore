<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>

    <!-- Google Fonts and Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


    <link rel="stylesheet" href="../build/css/estilosAlumnos.css">
</head>
<body>

<nav class="navbar navbar-expand-lg" >
  <img src="../src/img/header001.png" alt="Logo" style="width: 280px; position:absolute; margin-top:0; top:0.5px; margin-left:0px">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 30rem;">
      <b>Simula</b>Score
    </a>
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="logout.php" class="boton" style="padding: 2rem; background-color:#154c4b; text-decoration: none; color: #f1fcfb;">
        Cerrar sesión
      </a>
    </div>
  </div>
</nav>

<svg xmlns="http://www.w3.org/2000/svg" height="200px" width="200px" viewBox="0 0 200 200" class="pencil">
	<defs>
		<clipPath id="pencil-eraser">
			<rect height="30" width="30" ry="5" rx="5"></rect>
		</clipPath>
	</defs>
	<circle transform="rotate(-113,100,100)" stroke-linecap="round" stroke-dashoffset="439.82" stroke-dasharray="439.82 439.82" stroke-width="2" stroke="currentColor" fill="none" r="70" class="pencil__stroke"></circle>
	<g transform="translate(100,100)" class="pencil__rotate">
		<g fill="none">
			<circle transform="rotate(-90)" stroke-dashoffset="402" stroke-dasharray="402.12 402.12" stroke-width="30" stroke="hsl(223,90%,50%)" r="64" class="pencil__body1"></circle>
			<circle transform="rotate(-90)" stroke-dashoffset="465" stroke-dasharray="464.96 464.96" stroke-width="10" stroke="hsl(223,90%,60%)" r="74" class="pencil__body2"></circle>
			<circle transform="rotate(-90)" stroke-dashoffset="339" stroke-dasharray="339.29 339.29" stroke-width="10" stroke="hsl(223,90%,40%)" r="54" class="pencil__body3"></circle>
		</g>
		<g transform="rotate(-90) translate(49,0)" class="pencil__eraser">
			<g class="pencil__eraser-skew">
				<rect height="30" width="30" ry="5" rx="5" fill="hsl(223,90%,70%)"></rect>
				<rect clip-path="url(#pencil-eraser)" height="30" width="5" fill="hsl(223,90%,60%)"></rect>
				<rect height="20" width="30" fill="hsl(223,10%,90%)"></rect>
				<rect height="20" width="15" fill="hsl(223,10%,70%)"></rect>
				<rect height="20" width="5" fill="hsl(223,10%,80%)"></rect>
				<rect height="2" width="30" y="6" fill="hsla(223,10%,10%,0.2)"></rect>
				<rect height="2" width="30" y="13" fill="hsla(223,10%,10%,0.2)"></rect>
			</g>
		</g>
		<g transform="rotate(-90) translate(49,-30)" class="pencil__point">
			<polygon points="15 0,30 30,0 30" fill="hsl(33,90%,70%)"></polygon>
			<polygon points="15 0,6 30,0 30" fill="hsl(33,90%,50%)"></polygon>
			<polygon points="15 0,20 10,10 10" fill="hsl(223,10%,10%)"></polygon>
		</g>
	</g>
</svg>
<h2 style="padding:2rem ; margin:auto">Instrucciones Generales</h2>

    <div class="container--instrucciones">
    <h4>Antes de resolver el examen lee con atención estas instrucciones.No dudes en preguntar al aplicador cualquier duda que tengas. </h4>
    <br>    
        <h6>1. Lee detenidamente cada pregunta antes de seleccionar la respuesta. En cada pregunta hay cuatro opciones de respuesta identifiadas 
            con las letras A,B,C,D. Solo una es correcta.
        </h6>
        <br>
        <h6>
            2. Para contestar selecciona la respuesta que consideres correcta y de forma automatica se llenara tu hoja de respuestas.
        </h6>
        <br>
        <h6>
            3. Asegurate de elegir la respuesta correcta. Si te equivocas puedes seleccionar otra y esta se cambiara de forma automatica.
        </h6>
        <br>
        <h6>
            4. Si llegas a tener dudas al contestar tu examen, levanta la mano para que el aplicador se acerque y te aclare. <b>NO</b> te comuniques con nadie.
        </h6>
        <br>
        <h6>
            5. Puedes tener a la mano hojas en blanco para poder hacer tus anotaciones
        </h6>
        <br>
        <h6>
            6. No esta permitido utilizar cuadernos, tarjetas u hojas de apuntes.
        </h6>
        <br>
        <h6>
            7. El tiempo maximo para contestar este examen es de 3 horas.
        </h6>
        <br>
        <h6>
            8. Contesta todo tu examen, si hay preguntas que se te dificulten, no te detengas demasiado.
            Si otros compañeros terminan antes que tú, no te presiones ni te inquietes.
        </h6>
        <br>
        <h6>
            9. El simulador te avisara 10 minutos antes de que termine el tiempo establecido para responder.
        </h6>
        <br>
        <h6>
            10. Recuerda que <b>NO</b> puedes copiar o comunicarte on tus compañeros.
        </h6>
        <br>
        <h6>
            11. <b>NO puedes salir de la pagina del examen mientras lo estas contestando, ya que si lo haces se cerrara de forma automatica y te quedaras sin oportunidad para aplicarlo de nuevo.</b>
        </h6>
        <br>
        <h6>
            12. Al terminar de responder tu examen da click a ENVIAR y avisa a tu aplicador.
        </h6>

        <br>


        
        <a href="examenMejorEdu.php">
        <h2 style="padding:1rem ; margin:auto;background-color:#154c4b;width:50%; margin:auto; border-radius:10px;color:white;">PUEDES INICIAR</h2>
        </a>

    </div>

    

    <div class="seccion--imagen--indicaciones">

<img src="../src/img/buho2.png" alt="Img" >
</div>

</body>
</html>