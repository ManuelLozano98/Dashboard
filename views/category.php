<?php
require_once 'header.php';
?>

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
                            <h1>Categories</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Categories</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Category</h4>
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
                                            <input type="hidden" name="idcategory" id="idcategory">
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Write a name for a category" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="description" class="form-label fw-semibold">Description</label>
                                        <textarea id="description" class="form-control" rows="4" maxlength="255" oninput="updateCounter()" placeholder="Write here..."></textarea>
                                        <div class="text-end text-muted mt-1">
                                            <span id="counter">0/255</span>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="insert()">Save changes</button>
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
                                                    <h2 class="box-title">Category <button class="btn btn-success" id="addBtn" data-toggle="modal" data-target="#modal-default"><i class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableCategories"
                                                            class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>Id</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Active</th>
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
        </div><!-- /.content-wrapper -->


        <?php
        require_once "footer.php";
        ?>


    </div>

    <script src="<?php echo $ruta ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo $ruta ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="<?php echo $ruta ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="<?php echo $ruta ?>plugins/toastr/toastr.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $ruta ?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo $ruta ?>dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="../views/js/category.js"></script>
</body>

</html>