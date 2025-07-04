<?php 
  require_once '../configurations/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE ?> | Confirm</title>

    

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo PATHP?>plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo PATHP?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo PATHP?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo PATHP?>plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo PATHP?>plugins/toastr/toastr.min.css">
    <style>
        .button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            color: white;
        }

        .small {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #888;
        }

        .login-box {
            width: 425px;
        }

        .card {
            height: 500px;

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
                <div class="container">
                    <h2>Confirm your email address</h2>
                    <p>We've sent you a link to your email to verify your account.</p>
                    <a href="login" class="button">Go to log in</a>
                    <p class="small">Didn't receive the email? <a href="#">Resend</a></p>
                    <p>If you can't click the button, copy and paste the following link into your browser: <a
                            href="login">Link</a></p>
                </div>

            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo PATHP?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo PATHP?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo PATHP?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo PATHP?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo PATHP?>dist/js/adminlte.min.js"></script>
    <script src="views/js/utils.js"></script>
</body>

</html>