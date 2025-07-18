<?php    
require '..\db\connection.php';

    if(isset($_POST['register'])){
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim(($_POST['password']));
        $password2 = trim(($_POST['password2']));

        $buscar = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $buscar->execute([$email]);

        $error = '';

        if(empty($name) || empty($email) || empty($password)){
            $error  = 'Todos los campos necesitan ser llenados';
        }else if(strlen($name) < 3){
            $error = 'El nombre debe de ser mayor a 3 caracteres';
        }else if(!filter_var($email , FILTER_VALIDATE_EMAIL)){
            $error = 'El email no es valido';
        }else if($buscar->rowCount() > 0){
            $error = 'Ya hay un usuario registrado con es email';
        }else if($password !== $password2){ 
            $error = 'Las password no coinciden';
        }else if(strlen($password) < 6 ){
            $error = 'El password debe de ser mayor a 6 caracteres';
        }else{

            $password_hash = password_hash($password , PASSWORD_BCRYPT);

            $insertar = $conn->prepare("INSERT INTO usuarios(nombre,email,password) VALUES(?,?,?)");
            $insertar->execute([$name , $email , $password_hash]);

            header('Location: ..\tasks\list_tasks.php');
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="esp">
    <head>
        <meta charset="esp">
        <title>Registro</title>
        <link rel="stylesheet" href="..\assets\css\style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />

    </head>
    <body>
         <div class="login" id="login">
            <div class="form-conteiner">
            <h1>Registrar</h1>
            <?php if (!empty($error)): ?>
                 <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>  <!-- Usa htmlspecialchars para seguridad -->
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <label for="name">Nombre</label>
                <input type="text" name="name" placeholder="Nombre"><br>
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Correo"><br>
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password"><br>
                <label for="password2">Repetir Password</label>
                <input type="password" name="password2" placeholder="Repetir Password"><br>
                <button type="submit" name="register" value="Registrar">Registrar</button>
                <a href="../auth/login.php">Inicar sesion</a>
            </form>
        </div>
         </div>
    </body>
</html>