<?php 
  require_once '../configurations/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE ?> | Registration Page</title>
    
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
        .fa-eye-slash, .fa-eye{
            cursor: pointer;
        }
    </style>
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <a href="login"><b>Admin</b>Dashboard</a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">Register a new membership</p>

                <form id="form" method="post">
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input type="text" id="name" name="name" class="form-control" placeholder="Name">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user-circle"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input type="text" id="username" name="username" class="form-control"
                                placeholder="Username">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span id="eyepassword" class="fas fa-eye"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input type="password" id="repassword" name="repassword" class="form-control"
                                placeholder="Retype password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span id="eyerepassword" class="fas fa-eye"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                    <label for="agreeTerms">
                                        I agree to the <a href="#">terms</a>
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                </form>

                <p>Already have an account? <a href="login" class="text-center">Sign in</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.register-box -->

    <!-- jQuery -->
    <script src="<?php echo PATHP?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo PATHP?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo PATHP?>plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo PATHP?>plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?php echo PATHP?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo PATHP?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo PATHP?>dist/js/adminlte.min.js"></script>
    <script src="views/js/utils.js"></script>
    <script src="views/js/register.js"></script>
</body>

</html>