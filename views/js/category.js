/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getCategories();
}

function insert() {
  let category = {
    name: $("#name").val(),
    description: $("#description").val(),
  };
  $.ajax({
    url: "api/categories",
    type: "POST",
    dataType: "json",
    data: JSON.stringify(category),
    success: function (response) {
      if (response.status != "201") {
        if (response.details) {
          response.message += response.details.map((element) => {
            return `<br> ${Object.keys(element)} => ${Object.values(element)}`;
          });
        }
        toastr.warning(response.message);
      } else {
        toastr.success(response.message);
        getCategories();
      }
    },
    error: function (xhr, status, error) {
      let response = xhr.responseText;
      let parsed = null;

      try {
        parsed = JSON.parse(response);
      } catch (e) {
        toastr.error("Unexpected server error");
        console.error("Server response:", response);
        return;
      }

      let message = parsed.message || "Request error";

      if (parsed.details && Array.isArray(parsed.details)) {
        const detailMessages = parsed.details
          .map((item) => {
            return Object.values(item).join(", ");
          })
          .join("<br>");
        message += "<br>" + detailMessages;
      }

      toastr.error(message, `Error ${parsed.status} - ${parsed.error}`);
    },
  });
}

function deleteCategory(id) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: `api/categories?id=${id}`,
        type: "DELETE",
        success: function (result) {
          toastr.success(result.message);
          getCategories();
        },
      });
    }
  });
}
function edit(id) {
  let category = {
    id: "",
    name: $("#name").val(),
    description: $("#description").val(),
    active: $("#active").is("checked"),
  };
  $.ajax({
    url: "api/categories",
    type: "PUT",
    dataType: "json",
    data: JSON.stringify(category),
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
      url: "api/categories",
      type: "get",
      dataType: "json",
    },
    columns: [
      { data: "id_category" },
      { data: "name" },
      { data: "description" },
      { data: "active" },
      {
        data: null, // Column generated manually
        title: "Actions",
        orderable: false, // This column does not allow sorting
        searchable: false, // This column does not allow searching
        render: function (data, type, row) {
          return `
            <button class="btn btn-success btn-sm rounded-0" type="button" data-toggle="modal" data-target="#modal-edit-default" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
            <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete" onclick="deleteCategory(${data.id_category})"><i class="fa fa-trash"></i></button>
          `;
        },
      },
    ],
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
