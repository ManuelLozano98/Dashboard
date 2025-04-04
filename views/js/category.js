/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getCategories();
}

function insert() {
  $.ajax({
    url: "../controllers/category.php?do=save",
    type: "POST",
    data: {
      name: $("#name").val(),
      description: $("#description").val(),
    },
    success: function (result) {
      toastr.success(result);
      getCategories();
    },
  });
}

function getCategories() {
  $("#tableCategories").DataTable({
    buttons: [
      { extend: "copy", text: "Copy" },
      { extend: "csv", text: "CSV" },
      { extend: "excel", text: "Excel" },
      { extend: "pdf", text: "PDF" },
      { extend: "colvis", text: "Column visibility" },
    ],
    dom:
      "<'row'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" + // Up: Select + Buttons + Search
      "<'row'<'col-md-12'tr>>" + // Table
      "<'row'<'col-md-6'i><'col-md-6'p>>", // Down: Info + Pagination

    ajax: {
      url: "../controllers/category.php?do=categories",
      type: "get",
      dataType: "json",
    },
    columns: [
      { data: "id_category" },
      { data: "name" },
      { data: "description" },
      { data: "active" },
    ],
    error: function (e) {
      console.log(e.responseText);
    },
    success: function (s) {
      console.log(s);
    },
    bDestroy: true,
    iDisplayLength: 10,
    order: [[0, "desc"]],
    autoWidth: false,
    responsive: true,
  });
}
function updateCounter() {
  const limit = 255;
  $("#counter").text($("#description").val().length + "/" + limit);
  if ($("#description").val().length >= limit) {
    $("#counter").addClass("text-danger", "fw-bold");
  } else {
    $("#counter").removeClass("text-danger", "fw-bold");
  }
}
init();
