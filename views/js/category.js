/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getCategories();
  loadEditForm();
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
      getSuccessResponse(response);
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
function edit() {
  let category = {
    id: $("#edit-idcategory").val(),
    name: $("#edit-name").val(),
    description: $("#edit-description").val(),
    active: $("#customSwitch1").prop("checked")
  };
  $.ajax({
    url: "api/categories",
    type: "PUT",
    dataType: "json",
    data: JSON.stringify(category),
    success: function (result) {
      getSuccessResponse(result);
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
      { data: "id_category" },
      { data: "name" },
      { data: "description" },
      { data: "active" },
      getActionsColumnDataTable(),
    ]
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

function loadEditForm() {
  $('#tableCategories').on('click', '.edit-button', function () {
    let row = $(this).closest('tr');
    let table = $('#tableCategories').DataTable();
    if (row.hasClass('child')) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    $("#edit-name").val(data.name);
    $("#edit-idcategory").val(data.id_category);
    $("#edit-description").text(data.description);
    data.active === "0" ? $("#customSwitch1").prop("checked", false) : $("#customSwitch1").prop("checked", true);
  });
}

function getSuccessResponse(response) {
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
}

function getErrorResponse(xhr) {
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
}

init();
