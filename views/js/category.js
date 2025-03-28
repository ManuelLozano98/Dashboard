/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
    show(false);
    getCategories();

}

function show(bool) {
    if (bool) {
        $("#records").hide();
        $("#addCategories").show();
        $("#saveBtn").prop("disabled", false);
        $("#addBtn").hide();
    } else {
        $("#records").show();
        $("#addCategories").hide();
        $("#addBtn").show();
    }
}

function getCategories() {
    $("#tableCategories").DataTable(
            {
                buttons: [
                    "copy",
                    "csv",
                    "excel",
                    "pdf",
                    "colvis"
                ],
                "ajax":
                        {
                            url: '../controllers/category.php?do=categories',
                            type: 'get',
                            dataType: "json",
                        },
                columns: [
                    {"data": "id_category"},
                    {"data": "name"},
                    {"data": "description"},
                    {"data": "active"}
                ],
                error: function (e) {
                    console.log(e.responseText);
                },
                success: function (s) {
                    console.log(s);
                },
                "bDestroy": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "autoWidth": false,
                "responsive": true
            }).buttons().container('#tableCategories_wrapper .col-md-6:eq(0)');
}
init();
