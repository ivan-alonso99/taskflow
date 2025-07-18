<?php    

    session_start();

    require '..\db\connection.php';

    if (isset($_POST['eliminar'])) {
        $id_task = $_POST['id_task'];
         
        $sql = $conn->prepare("DELETE FROM tareas WHERE id=?");
        $error = '';
        if ($sql->execute([$id_task])) {
            echo 'Tarea eliminada con exito';
            header('Location: ..\tasks\list_tasks.php');
        }else{
            $error = 'Error para eliminar';
            header('Location: ..\tasks\list_tasks.php');
        }

    }
?>