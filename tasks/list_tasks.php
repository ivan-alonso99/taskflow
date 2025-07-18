<!DOCType html>
<html lang="esp">
   <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="..\assets\css\style.css">
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <script src="../assets/js/script.js"></script>
      <title>Lista de tareas</title>
   </head>
   <body>
<div id="contenido">
    <div id="box">
        <div class="titulo" id="titulo">
            <img src="../assets/images/Logo_Taskflow.png" alt="Logo de la empresa" class="logo">
        </div><!--Titulo-->
     <div class="menu" id="menu">
        <nav class="nav">
        <ul>
            <li><a href=#titulo>Inicio</a></li>
            <li><a href="#tareas_pendientes">Tareas pendientes</a></li>
            <li><a href="#tareas_ProxVencer">Proximas a vencer</a></li>
            <li><a href="#tareas_vencidas">Tareas vencidas</a></li>
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
    <div class="logout">
        <form action="../auth/logout.php" method="POST">
            <button type="submit" name="logout" value="logout">Cerrar Sesion</button>
        </form>
    </div>

    </div><!--box-->
    
    <div class="lista_tareas" id="lista_tareas">
        <h2>Lista de tareas</h2>
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
   </body>
</html>

<?php     
   session_start();

   require "..\db\connection.php";

   if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
        header('Location: ../auth/login.php');
        exit();
   }

   if(isset($_SESSION['notificacion'])) {
    $notif = $_SESSION['notificacion'];
    unset($_SESSION['notificacion']);
    echo "<script>mostrarNotificacion('".addslashes($notif['mensaje'])."', '".$notif['tipo']."');</script>";
}

   $id_usuario = $_SESSION['id_usuario'];
   echo "<div class='tabla' id='tabla'>";
   echo "<table>";
   echo "<tr><th>Titulo</th><th>Descripcion</th><th id='prioridad'>Prioridad</th><th id='estado'>Estado</th><th id='editar'>Editar</th><th id='eliminar'>Eliminar</th></tr>";
   $sql = $conn->prepare("SELECT tareas.id,tareas.titulo,tareas.descripcion,tareas.fecha_creacion,tareas.fecha_vencimiento,tareas.prioridad,tareas.estado,categorias.color 
                          FROM tareas 
                          INNER JOIN categorias ON tareas.categoria_id = categorias.id
                          where tareas.usuario_id = ?");
   $sql->execute([$id_usuario]);


   $estado = $conn->prepare("SELECT estado FROM tareas");
   $estado->execute();
   $row2 = $estado->fetch(PDO::FETCH_ASSOC);

   if($sql->rowCount() > 0){
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            echo "<tr bgcolor='".$row['color']."'>
                 <td>".$row['titulo']."</td>
                 <td>".$row['descripcion']."</td>
                 <td>".$row['prioridad']."</td>
                  <form method='POST' action='edit_task.php'>
                 <td>
                    <input type='hidden' name='id_estado' value='".$row['id']."'>
                    <button type='submit' id='ButtonEstado' name='estado' value='".$row['estado']."'>".$row['estado']."</button>
                  </td>
                    </form>
                 <form method='GET' action='edit_task.php'>
                 <td><input type='hidden' name='id_task' value='".$row['id']."'>
                     <button type='submit'  id='editarButton' name='editar' value='Editar'><img src='../assets/images/boton-editar.png'></button></td>
                  </form>
                 <form method='POST' action='delete_task.php'> 
                <td><input type='hidden' name='id_task' value='".$row['id']."'>
                    <button type='submit' id='eliminarButton' name='eliminar' value='Eliminar'><img src='../assets/images/trash.png'></button></td>
                    </form>
                  </tr>";
        }
       
   }else{
    echo "<tr>
              <td>No hay tareas registradas</td>
         </tr>";
   }
   echo "</table>";

   echo "<form method='POST' action='add_task.php'>";
   echo "<button type='submit' id='ButtonAdd' name='addWork' value='Agregar tarea'>Agregar Tarea</button>";
   echo "</form>";

   echo "<form method='POST' action='pdf.php'>";
   echo "<button type='submit' id='ButtonPDF' name='addWork' value='Agregar tarea'>PDF</button>";
   echo "</form>";
   echo "</div>";

?>

  <div class="olas">
        <img src="../assets/images/ola1.png" id="olas">
    </div>

    <div class="dashboard">

        <h1>Dashboard</h1>

            <div class="tabla" id='cantidadTareas'>
                <h2>Cantidad de tareas</h2>
                <?php $cantidad = $conn->prepare("SELECT COUNT(id) as cantidad FROM tareas where tareas.usuario_id = ?");  
                    $cantidad->execute([$id_usuario]);
                    $result = $cantidad->fetch(PDO::FETCH_ASSOC);
                    ?>
                 <p>Cantidad: <span><?php echo $result['cantidad']?></span></p>

            </div>

            <div class="tabla" id='tareas_pendientes'>
                <h2>Tareas pendientes</h2>
                <?php $pendientes = $conn->prepare("SELECT COUNT(id) as cantidad FROM tareas");  
                    $cantidad->execute();
                    $result = $cantidad->fetch(PDO::FETCH_ASSOC);
                       $id_usuario = $_SESSION['id_usuario'];
   
   echo "<table>";
echo "<tr><th>Titulo</th><th>Descripcion</th><th id='prioridad'>Prioridad</th><th id='estado'>Estado</th><th id='editar'>Editar</th><th id='eliminar'>Eliminar</th></tr>";   
    $sql = $conn->prepare("SELECT tareas.id,tareas.titulo,tareas.descripcion,tareas.fecha_creacion,tareas.fecha_vencimiento,tareas.prioridad,tareas.estado,categorias.color 
                          FROM tareas 
                          INNER JOIN categorias ON tareas.categoria_id = categorias.id
                          where tareas.usuario_id = ? AND
                          tareas.estado = ?");
   $sql->execute([$id_usuario , 'pendiente']);


   $estado = $conn->prepare("SELECT estado FROM tareas");
   $estado->execute();
   $row2 = $estado->fetch(PDO::FETCH_ASSOC);

   if($sql->rowCount() > 0){
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            echo "<tr bgcolor='".$row['color']."'>
                 <td>".$row['titulo']."</td>
                 <td>".$row['descripcion']."</td>
                 <td>".$row['prioridad']."</td>
                  <form method='POST' action='edit_task.php'>
                 <td>
                    <input type='hidden' name='id_estado' value='".$row['id']."'>
                    <button type='submit' id='ButtonEstado' name='estado' value='".$row['estado']."'>".$row['estado']."</button>
                  </td>
                    </form>
                 <form method='GET' action='edit_task.php'>
                 <td><input type='hidden' name='id_task' value='".$row['id']."'>
                     <button type='submit'  id='editarButton' name='editar' value='Editar'><img src='../assets/images/boton-editar.png'></button></td>
                  </form>
                 <form method='POST' action='delete_task.php'> 
                <td><input type='hidden' name='id_task' value='".$row['id']."'>
                    <button type='submit' id='eliminarButton' name='eliminar' value='Eliminar'><img src='../assets/images/trash.png'></button></td>
                    </form>
                  </tr>";
        }
       
   }else{
    echo "<tr>
              <td>No hay tareas registradas</td>
         </tr>";
   }
   echo "</table>";
   echo "<form method='POST' action='add_task.php'>";
   echo "<input type='submit' id='ButtonAdd' name='addWork' value='Agregar tarea'>";
   echo "</form>";
                    ?>
            </div>

            <div class="tabla" id='tareas_ProxVencer'>

            <h2>Tareas proximas a vencer</h2>
                    <?php  
                       $id_usuario = $_SESSION['id_usuario'];
   
   echo "<table>";
echo "<tr><th>Titulo</th><th>Descripcion</th><th id='prioridad'>Prioridad</th><th id='estado'>Estado</th><th id='editar'>Editar</th><th id='eliminar'>Eliminar</th><th id='fecha'>Fecha</th></tr>";   
    $sql = $conn->prepare("SELECT tareas.id,tareas.titulo,tareas.descripcion,tareas.fecha_creacion,tareas.fecha_vencimiento,tareas.prioridad,tareas.estado,categorias.color 
                          FROM tareas 
                          INNER JOIN categorias ON tareas.categoria_id = categorias.id
                          where tareas.usuario_id = ? and
                          tareas.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 2 DAY) 
                          order by tareas.fecha_vencimiento asc ");
   $sql->execute([$id_usuario]);


   $estado = $conn->prepare("SELECT estado FROM tareas");
   $estado->execute();
   $row2 = $estado->fetch(PDO::FETCH_ASSOC);

   if($sql->rowCount() > 0){
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            echo "<tr bgcolor='".$row['color']."'>
                 <td>".$row['titulo']."</td>
                 <td>".$row['descripcion']."</td>
                 <td>".$row['prioridad']."</td>
                  <form method='POST' action='edit_task.php'>
                 <td>
                    <input type='hidden' name='id_estado' value='".$row['id']."'>
                    <button type='submit' id='ButtonEstado' name='estado' value='".$row['estado']."'>".$row['estado']."</button>
                  </td>
                    </form>
                 <form method='GET' action='edit_task.php'>
                 <td><input type='hidden' name='id_task' value='".$row['id']."'>
                     <button type='submit'  id='editarButton' name='editar' value='Editar'><img src='../assets/images/boton-editar.png'></button></td>
                  </form>
                 <form method='POST' action='delete_task.php'> 
                <td><input type='hidden' name='id_task' value='".$row['id']."'>
                    <button type='submit' id='eliminarButton' name='eliminar' value='Eliminar'><img src='../assets/images/trash.png'></button></td>
                    </form>
                <td>".$row['fecha_vencimiento']."</td>
                  </tr>";
        }
       
   }else{
    echo "<tr>
              <td>No hay tareas a vencer en los proximos 3 dias</td>
         </tr>";
   }
   echo "</table>";
   echo "<form method='POST' action='add_task.php'>";
   echo "<input type='submit' id='ButtonAdd' name='addWork' value='Agregar tarea'>";
   echo "</form>";
                    ?>
            </div>
            <div class="tabla" id='tareas_vencidas'>
                   <h2>Tareas vencidas</h2>
                    <?php  
                       $id_usuario = $_SESSION['id_usuario'];
   
   echo "<table>";
echo "<tr><th>Titulo</th><th>Descripcion</th><th id='prioridad'>Prioridad</th><th id='estado'>Estado</th><th id='editar'>Editar</th><th id='eliminar'>Eliminar</th><th id='fecha' >Fecha</th></tr>";   
    $sql = $conn->prepare("SELECT tareas.id,tareas.titulo,tareas.descripcion,tareas.fecha_creacion,tareas.fecha_vencimiento,tareas.prioridad,tareas.estado,categorias.color 
                          FROM tareas 
                          INNER JOIN categorias ON tareas.categoria_id = categorias.id
                          where tareas.usuario_id = ? and
                          tareas.fecha_vencimiento < CURDATE()");
   $sql->execute([$id_usuario]);


   $estado = $conn->prepare("SELECT estado FROM tareas");
   $estado->execute();
   $row2 = $estado->fetch(PDO::FETCH_ASSOC);

   if($sql->rowCount() > 0){
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            echo "<tr bgcolor='".$row['color']."'>
                 <td>".$row['titulo']."</td>
                 <td>".$row['descripcion']."</td>
                 <td>".$row['prioridad']."</td>
                  <form method='POST' action='edit_task.php'>
                 <td>
                    <input type='hidden' name='id_estado' value='".$row['id']."'>
                    <button type='submit' id='ButtonEstado' name='estado' value='".$row['estado']."'>".$row['estado']."</button>
                  </td>
                    </form>
                 <form method='GET' action='edit_task.php'>
                 <td><input type='hidden' name='id_task' value='".$row['id']."'>
                     <button type='submit'  id='editarButton' name='editar' value='Editar'><img src='../assets/images/boton-editar.png'></button></td>
                  </form>
                 <form method='POST' action='delete_task.php'> 
                <td><input type='hidden' name='id_task' value='".$row['id']."'>
                    <button type='submit' id='eliminarButton' name='eliminar' value='Eliminar'><img src='../assets/images/trash.png'></button></td>
                    </form>
                <td>".$row['fecha_vencimiento']."</td>
                  </tr>";
        }
       
   }else{
    echo "<tr>
              <td>No hay tareas vencidas</td>
         </tr>";
   }
   echo "</table>";
   echo "<form method='POST' action='add_task.php'>";
   echo "<input type='submit' id='ButtonAdd' name='addWork' value='Agregar tarea'>";
   echo "</form>";
                    ?>
            </div>
    </div>
</div>

  <div class="olasEnd">
        <img src="../assets/images/olas2.png" id="olasEnd">
    </div>