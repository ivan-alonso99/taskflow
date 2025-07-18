<?php   
    session_start();

    require '..\db\connection.php';

    if(isset($_POST['login'])){
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $buscar = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $buscar->execute([$email]);
        $datos_usuario = $buscar->fetch(PDO::FETCH_ASSOC);

        $error = '';
        if(empty($email) || empty($password)){
            $error = 'Todos los campos tiene que estar llenos';
        }else if(!filter_var($email , FILTER_VALIDATE_EMAIL)){
            $error = 'Email no valido';
        }else if($buscar->rowCount() < 0 ){
            $error = 'No existe ningun ususario con ese correo';
        }else if(password_verify($password , $datos_usuario['password']) && $datos_usuario ){

            $_SESSION = [
                'id_usuario' => $datos_usuario['id'],
                'user_name' => $datos_usuario['nombre'],
                'user_email' => $datos_usuario['email'],
                'logged_in' => true
            ];

            // Guardar notificación para mostrar después del redirect
            $_SESSION['notificacion'] = [
            'mensaje' => '¡Bienvenido ' . $datos_usuario['nombre'] . '!',
            'tipo' => 'success'
        ];

            header('Location: ..\tasks\list_tasks.php');
            exit();
            
        }else{
            $error = 'Usuario o contraseña incorrectos';
        }
    }
?>

<!DOCTYPE html>
<html lang="esp">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link rel="stylesheet" href="..\assets\css\style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />

    </head> 
    <body>
         <div id="notificacion"></div>
        <div class="login" id="login">
            <div class="form-conteiner">
            <?php if (!empty($error)): ?>
            <script src="../assets/js/notifications.js"></script>
            <?php endif; ?>
            <h1>Login</h2>
            <form action="" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Email"><br>
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="password"><br>
                <button type="submit" name="login" value="Iniciar">Iniciar</button>
                <a href="../auth/register.php">Registrar</a>
            </form>
            </div>
        </div>
        <script src="..\assets\js\script.js"></script>
            <script>
    <?php if(!empty($error)): ?>
        mostrarNotificacion('<?= addslashes($error) ?>');
    <?php endif; ?>
    
    <?php 
    // Mostrar notificación guardada en sesión (para redirecciones)
    if(isset($_SESSION['notificacion'])): 
        $notif = $_SESSION['notificacion'];
        unset($_SESSION['notificacion']);
    ?>
        mostrarNotificacion('<?= addslashes($notif['mensaje']) ?>', '<?= $notif['tipo'] ?>');
    <?php endif; ?>
    </script>
    </body>
</html>