<?php
session_start();
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
include('config/db.php');

// 1. REDIRECT JIKA SUDAH LOGIN
// Jika pengguna sudah login, langsung arahkan ke halaman utama
if (isset($_SESSION['nama_log'])) {
    header("Location: employee.php");
    exit;
}

$error_message = ""; // Variabel untuk menyimpan pesan error

// 2. PROSES LOGIN
if(isset($_POST['tSubmit'])){
    $Nama_Login = $_POST['nama_login'];
    $Pass = $_POST['password'];

    if (empty($Nama_Login) || empty($Pass)) {
        $error_message = "Nama dan Password Harus Diisi";
    } else {
        // 3. PERBAIKAN KEAMANAN (PENTING!)
        // Menggunakan PREPARED STATEMENTS untuk mencegah SQL Injection
        $sql_prepare = mysqli_prepare($con, "SELECT * FROM admin WHERE nama_login = ? AND password = ?");
        mysqli_stmt_bind_param($sql_prepare, "ss", $Nama_Login, $Pass); 
        mysqli_stmt_execute($sql_prepare);
        $sql = mysqli_stmt_get_result($sql_prepare);

        if(mysqli_num_rows($sql) == 0){
            $error_message = "Nama atau Password salah";
        } else {
            // Update last login
            $sql_update = mysqli_prepare($con, "UPDATE `admin` SET `last_login`=CURRENT_TIMESTAMP WHERE nama_login = ?");
            mysqli_stmt_bind_param($sql_update, "s", $Nama_Login);
            mysqli_stmt_execute($sql_update);

            $row = mysqli_fetch_assoc($sql);
            $_SESSION['nama_log'] = $row['nama_login'];
            $_SESSION['pass'] = $row['password']; 
            
            header("Location: employee.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTI Absensi | Login</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  include('scriptcss.php') 
  ?>
  <?php

  ?>
</head>
<body class="hold-transition sidebar-mini login-page">
<div class="wrapper">
<?php
include('header.php'); 
?>
<?php
include('sidebar.php');
?>

  <div class="content-wrapper">
    <section class="content">
    	<br>
    	<div class="row">
    		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
    			
                <div class="login-card">
                    <div class="login-card-header">
                        <img class="img-responsive" alt="Logo MTI" src="dist/img/logomti.jpg">
                    </div>
                    <div class="login-card-body">
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" style="border-radius: 8px;">
                                <i class="fa fa-ban"></i> <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form role="form" name="LoginF" action="index.php" method="POST">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Nama Pegawai" name="nama_login" type="text" autofocus="" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="Pass" placeholder="Password" name="password" type="password" required>
                                </div>
                                
                                <div class="login-links">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" onclick="myFunction()"> Show Password
                                        </label>
                                    </div>
                                    <span><a href="forgetpwd.php">Lupa Password?</a></span>
                                </div>
                                
                                <br>
                                <input type="Submit" class="btn btn-primary btn-block" name="tSubmit" value=" Login ">
                            </fieldset>
                        </form>
                    </div>
                </div>

    		</div></div><script>
    	function myFunction() {
    	    var x = document.getElementById("Pass");
    	    if (x.type === "password") {
    	        x.type = "text";
    	    } else {
    	        x.type = "password";
    	    }
    	}
    	</script>
    </section>
    </div>
  <?php
  ?>

</div>
</body>
</html>