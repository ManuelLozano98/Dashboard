/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  loadModalByURL();
  getCategories();
  loadCounter();
  loadButtonsAction();
  loadEditForm();
}

function loadModalByURL() {
  let params = new URLSearchParams(window.location.search);
  if (params.get("openModal") === "true") {
    $('#modal-default').modal('show');
  }
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
      getSuccessResponse(response, getCategories);
    },
    error: function (xhr) {
      getErrorResponse(xhr);
    },
  });
}

function deleteItem(id) {
  getDeleteMsg().then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: `api/categories/${id}`,
        type: "DELETE",
        dataType: "json",
        success: function (result) {
          toastr.success(result.message);
          getCategories();
        },
        error: function (xhr) {
          getErrorResponse(xhr);
        },
      });
    }
  });
}
function edit() {
  let category = {
    id: parseInt($("#edit-idcategory").val()),
    name: $("#edit-name").val(),
    description: $("#edit-description").val(),
    active: $("#customSwitch1").prop("checked") ? 1 : 0
  };
  $.ajax({
    url: "api/categories",
    type: "PUT",
    dataType: "json",
    data: JSON.stringify(category),
    success: function (result) {
      getSuccessResponse(result, getCategories);
    },
    error: function (xhr) {
      getErrorResponse(xhr);
    },
  });
}

function getCategories() {
  $("#tableCategories").DataTable({
    ...getSettingsDataTable(),
    buttons: getButtonsDataTable(),
    dom: getDomStyleDataTable(),
    ajax: {
      url: "api/categories",
      type: "get",
      dataType: "json",
    },
    columns: [
      { data: "id" },
      { data: "name" },
      { data: "description" },
      { data: "active" },
      getActionsColumnDataTable(),
    ]
  });
}

function loadEditForm() {
  $('#tableCategories').on('click', '.edit-button', function () {
    let row = $(this).closest('tr');
    let table = $('#tableCategories').DataTable();
    if (row.hasClass('child')) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    $("#edit-name").val(data.name);
    $("#edit-idcategory").val(data.id);
    $("#edit-description").val(data.description);
    updateCounter("edit-description", "edit-counter");
    data.active === 0 ? $("#customSwitch1").prop("checked", false) : $("#customSwitch1").prop("checked", true);
  });
}

init();
