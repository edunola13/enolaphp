<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo $view->urlResourceFor('css/bootstrap.min.css');?>" rel="stylesheet">
    <!-- Bootstrap JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo $view->urlResourceFor('js/bootstrap.min.js');?>"></script>
</head>
<body style="padding-top: 50px;">
  <div class="container">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo $view->urlFor('');?>">Enola PHP</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="http://www.edunola.com.ar/enolaphp/doc">Documentación</a></li>
            <li><a href="http://www.edunola.com.ar/enolaphp/nosotros">Nosotros</a></li>
            <li><a href="http://www.edunola.com.ar/enolaphp/contacto">Contacto</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    <section style="text-align: center;">
        <h1>Bienvenido a Enola Framework PHP</h1>
        <p class="lead">Felicitaciones, ya se encuentra en funcionamiento el framework Enola PHP.</p>
    </section>      
  </div>
</body>
</html>