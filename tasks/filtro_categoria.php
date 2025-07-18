    <!DOCType html>
    <html lang="esp">
    <head>
        <meta charset="utf-8">
        <title>Lista de tareas</title>
        <link rel="stylesheet" href="..\assets\css\style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />

    </head>
    <body>
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
            <option value="categoria" selected>Categoria</option>
            <option value="prioridad">Prioridad</option>
        </select>
            <input type="submit" name='filter' value="🔍︎">
      </form>
    </div><!--filtro-->  

        <form id="categoria" method="POST">
    <label for="categoria"></label>
    <select name="categoria" id="categoria" onchange="guardarSeleccion()">
        <option value="1" <?= ($_POST['categoria'] ?? '') == 'trabajo' ? 'selected' : ''?>>Trabajo</option>
        <option value="2" <?= ($_POST['categoria'] ?? '') == 'personal' ? 'selected' : ''?>>Personal</option>
        <option value="3" <?= ($_POST['categoria'] ?? '') == 'estudia' ? 'selected' : ''?>>Estudio</option>
    </select>
  
    <input type="submit" name='filter2' value="🔍︎">
</form>

        </div><!--box-->

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
        header('Location: auth\login.php ');
        exit();
   }


   if(isset($_POST['filter2'])){
        $filtro = $_POST['categoria'];

                $id_usuario = $_SESSION['id_usuario'];
                echo "<div class='tabla' id='tabla'>";
                echo "<table>";
                echo "<tr><th>Titulo</th><th>Descripcion</th><th id='prioridad'>Prioridad</th><th id='estado'>Estado</th><th id='editar'>Editar</th><th id='eliminar'>Eliminar</th></tr>";
                $sql = $conn->prepare("SELECT tareas.id,tareas.titulo,tareas.descripcion,tareas.fecha_creacion,tareas.fecha_vencimiento,tareas.prioridad,tareas.estado,categorias.color 
                                        FROM tareas 
                                        INNER JOIN categorias ON tareas.categoria_id = categorias.id
                                        where tareas.usuario_id = ? and
                                        tareas.categoria_id = ?");

                $sql->execute([$id_usuario , $filtro]);

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
                echo "</div>";

        }
   

?>
