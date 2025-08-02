<?php
require_once __DIR__ . '/../configurations/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE ?> | Roles</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo PATHP ?>dist/css/adminlte.min.css">
    <!-- Datatable -->
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/sweetalert2/sweetalert2.min.css">
    <!-- Select 2 -->
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo PATHP ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <style>
        .roles {
            cursor: pointer;
        }

        .roles:hover {
            background-color: #dc3545 !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        require_once "navbar.php";
        require_once "aside.php";
        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Roles</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home">Home</a></li>
                                <li class="breadcrumb-item active">Roles</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Role</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="form" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="hidden" name="idrole" id="idrole">
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Write a name for a role" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="users" class="form-label fw-semibold">Users to add the role
                                            to:</label>
                                        <p><small>If you don't want to add any user yet, don't select anything.</small>
                                        </p>
                                        <div class="select2-green">
                                            <select class="select2" multiple="multiple" data-placeholder="Select Users"
                                                data-dropdown-css-class="select2-green" style="width: 100%;"
                                                name="users[]" id="users">
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="save" type="button" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="modal-edit-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Role</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form-edit" id="form-edit" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="edit-name">Name</label>
                                            <input type="hidden" name="id" id="edit-idrole">
                                            <input type="text" class="form-control" name="name" id="edit-name"
                                                placeholder="Write a name for a role" value="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="edit-save" type="button" class="btn btn-primary">Save changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->


            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <div class="box-tools pull-right">
                                </div>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="box-title">Role <button class="btn btn-success"
                                                            id="addBtn" data-toggle="modal"
                                                            data-target="#modal-default"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableRoles" class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>Id</th>
                                                                <th>Name</th>
                                                                <th>Actions</th>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>


                                                </div><!-- /.card-body -->
                                            </div><!-- /.card -->
                                        </div><!-- /.col-12 -->
                                    </div><!-- /.row -->
                                </div><!-- /.container-fluid -->
                            </div><!-- /.box-header -->
                        </div><!-- /.box -->
                    </div><!-- /.cold-md-12 -->
                </div><!-- /.row -->
            </section><!-- /.content -->

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <div class="box-tools pull-right">
                                </div>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="box-title">Users with Roles <button
                                                            class="btn btn-success" id="addUserRolesBtn"
                                                            data-toggle="modal" data-target="#modal-add-user-roles"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableUsersRoles"
                                                            class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>User</th>
                                                                <th>Roles</th>
                                                                <th>Actions</th>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                        <tfoot>
                                                            <tr>
                                                                <td>
                                                                    <p style="color: red;">* If you click on the role name, you can delete it.</p>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </div>


                                                </div><!-- /.card-body -->
                                            </div><!-- /.card -->
                                        </div><!-- /.col-12 -->
                                    </div><!-- /.row -->
                                </div><!-- /.container-fluid -->
                            </div><!-- /.box-header -->
                        </div><!-- /.box -->
                    </div><!-- /.cold-md-12 -->
                </div><!-- /.row -->
            </section><!-- /.content -->


            <div class="modal fade" id="modal-add-user-roles">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Roles to User</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="form" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="users-roles">Users</label>
                                        <div class="select2-green">
                                            <select class="select2" multiple="multiple" data-placeholder="Select Users"
                                                data-dropdown-css-class="select2-green" style="width: 100%;"
                                                name="users[]" id="users-roles">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="roles-users">Roles</label>
                                            <input type="hidden" name="idrole" id="idrole">
                                            <div class="select2-green">
                                                <select class="select2" multiple="multiple"
                                                    data-placeholder="Select Roles"
                                                    data-dropdown-css-class="select2-green" style="width: 100%;"
                                                    name="roles[]" id="roles-users">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="saveUser" type="button" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>


            <div class="modal fade" id="modal-add-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Roles to this User</h4>
                            <input type="hidden" value="" id="thisUser" />
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="form" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="roles-users2">Role</label>
                                            <input type="hidden" name="idrole" id="idrole">
                                            <div class="select2-green">
                                                <select class="select2" multiple="multiple"
                                                    data-placeholder="Select Roles"
                                                    data-dropdown-css-class="select2-green" style="width: 100%;"
                                                    name="roles[]" id="roles-users2">
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="saveRole" type="button" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

        </div><!-- /.content-wrapper -->


        <?php
        require_once "footer.php";
        ?>


    </div>

    <script src="<?php echo PATHP ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo PATHP ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="<?php echo PATHP ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo PATHP ?>plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo PATHP ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo PATHP ?>dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/utils.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="views/js/role.js"></script>
    <script>
        $('.select2').select2();
    </script>
</body>

</html>