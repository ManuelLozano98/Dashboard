<?php 
  require_once '../configurations/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE ?> | Confirm registration</title>

    

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo PATHP?>plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo PATHP?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo PATHP?>dist/css/adminlte.min.css">
    <style>
        .login-box {
            width: 425px;
        }

        .card {
            height: 400px;

        }

        .container {
            text-align: center;
        }

        .loader {
            border: 5px solid #f3f3f3;
            animation: spin 1s linear infinite;
            border-top: 5px solid #555;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-top: 50px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .hide-loader {
            display: none;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card">
            <div class="login-logo">
                <a href="login"><b>Admin</b>Dashboard</a>
            </div>
            <div class="card-body login-card-body">
                <div class="container" id="verified">
                    <img src="files/users_img/istockphoto-1416145560-612x612.jpg" width="100px" height="100px" />
                    <h2>Email Verified</h2>
                    <p>Your email address was successfully verified.</p>
                </div>
                <div class="container" id="not-verified" style="display:none">
                    <img src="files/users_img/error-icon-4.png" width="100px" height="100px" />
                    <h2>Email could not be verified</h2>
                    <p>Please make sure that you have entered the information we have sent you correctly.</p>
                </div>
                <div class="container hide-loader" id="redirect">
                    <div class="container loader" id="loader">
                    </div>
                    <p>Being automatically redirected to log in...</p>
                </div>
            </div>
            <!-- /.login-card-body -->
        </div>
        <!-- /.card -->
    </div>

    <!-- jQuery -->
    <script src="<?php echo PATHP?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo PATHP?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo PATHP?>dist/js/adminlte.min.js"></script>
    <script>
        if (window.location.search && window.location.search === "?user=not-registered") { // If the token is not valid show error
            document.getElementById("verified").style.display = "none";
            document.getElementById("not-verified").style.display = "block";
        }
        else {
            document.getElementById("redirect").classList.remove("hide-loader");
            setTimeout(() => {
                location.href = "login";
            }, 3000);
        }

    </script>
</body>

</html>