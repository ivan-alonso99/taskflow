<?php  
session_start();
require '..\db\connection.php';

    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ) {
    header("Location: index.php");
    exit();
}

  if(isset($_POST['add'])){
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_Vencimiento = $_POST['date'];
    $prioridad = $_POST['prioridad'];
    $id_usuario = $_SESSION['id_usuario'];
    $id_categoria = $_POST['categoria'];

    date_default_timezone_set('America/Mexico_City');
    $fecha_creacion = date("Y-m-d H:i:s");

    $error = '';
    
    if(empty($titulo) || empty($descripcion) || empty($fecha_Vencimiento || empty($prioridad) || empty($id_categoria))){
        $error = 'No has llenado todos los campos';
    }else if($fecha_Vencimiento < $fecha_creacion){
        $error = 'La fecha de vencimiento no puede ser menor a la actual';
    }else{
        $sql = $conn->prepare("INSERT INTO tareas(usuario_id,categoria_id,titulo,descripcion,fecha_creacion,fecha_vencimiento,prioridad)
                                VALUES(?,?,?,?,?,?,?)");
       if($sql->execute([$id_usuario,$id_categoria,$titulo,$descripcion,$fecha_creacion,$fecha_Vencimiento,$prioridad])){
            header('Location: ..\tasks\list_tasks.php');
            exit();
       }
    
    }

  }
?>

<!DOCTYPE html>
<html lang="esp">
    <head>
        <meta charset="utf-8">
        <title>Agregar tarea</title>
        <link rel="stylesheet" href="..\assets\css\style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
    </head>
    <body>
    <div id="box">
        <div class="titulo" id="titulo">
            <img src="../assets/images/Logo_Taskflow.png" alt="Logo de la empresa" class="logo">
        </div><!--Titulo-->
     <div class="menu" id="menu">
        <nav class="nav">
        <ul>
            <li><a href="..\tasks\list_tasks.php">Inicio</a></li>
            <li><a href="..\tasks\list_tasks.php#tareas_pendientes">Tareas pendientes</a></li>
            <li><a href="..\tasks\list_tasks.php#tareas_ProxVencer">Proximas a vencer</a></li>
            <li><a href="..\tasks\list_tasks.php#tareas_vencidas">Tareas vencidas</a></li>
        </ul>
        </nav>
     </div><!--menu-->

    <div class="buscador" id="buscador">
      <form action="busqueda.php" method="POST">
         <label for="word"></label>
         <input type="text" name="word" placeholder="Busca un titulo o descripcion">
         <input type="submit" name='search' value="search">
      </form>
    </div><!--buscador-->

    <div class="filtro">
      <form id="miFormulario" method="POST">
            <label for="filtro">Filtrar</label>
        <select name="filtro" id="filtro" onchange="cambiarAction()">
            <option value="estado">Estado</option>
            <option value="categoria">Categoria</option>
            <option value="prioridad">Prioridad</option>
        </select>
            <input type="submit" name='filter' value="🔍︎">
      </form>
    </div><!--filtro-->  

    </div><!--box-->
    
    <div class="lista_tareas" id="lista_tareas">
        <h2>Agregar Tarea</h2>
    </div><!--lista de tareas-->



<script>
function cambiarAction() {
    const formulario = document.getElementById('miFormulario');
    const filtro = document.getElementById('filtro').value;
    
    // Asignar diferentes acciones según la opción seleccionada
    switch(filtro) {
        case 'estado':
            formulario.action = 'filtro_estado.php';
            break;
        case 'categoria':
            formulario.action = 'filtro_categoria.php';
            break;
        case 'prioridad':
            formulario.action = 'filtro_prioridad.php';
            break;
        default:
            formulario.action = 'list_tasks.php';
    }
    
    // Opcional: Mostrar en consola para depuración
    console.log('Action cambiado a: ' + formulario.action);
}

// Llamar a la función al cargar la página para establecer el action inicial
cambiarAction();
</script>

        <div class="formulario" id="formulario">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                 <?= htmlspecialchars($error) ?>  <!-- Usa htmlspecialchars para seguridad -->
             </div>
        <?php endif; ?>
            <form action="" method="POST">
                <label for="titulo">Titulo</label>
                <input type="text" name="titulo" placeholder="Titulo"><br>
                <label for="Descripcion">Descripcion</label>
                <input type="text" name="descripcion" placeholder="Descripcion"><br>
                <label for="vencimiento">Fecha de vencimiento</label>
                <input type="date" name="date" placeholder="Fecha de vencimiento" require><br>
                <label for="prioridad">Prioridad</label>
                <select name="prioridad">
                    <option value="alta">Alta</option>
                    <option value="media" selected>Media</option>
                    <option value="Baja">Baja</option>
                </select><br>
                <label for="categoria">Categoria</label>
                <select name="categoria" id="categoria">
                    <?php    
                    $sql = $conn->prepare("SELECT id,nombre FROM categorias");
                    $sql->execute();

                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        echo "<option value='".$row['id']."'>".$row["nombre"]."</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="add" value="Agregar Tarea">Agregar Tarea</button>
            </form>
        </div> <!-- Formulario  -->
        

    </body>
</html>