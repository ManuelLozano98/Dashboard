/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getArticles();
  document.getElementById("tab2-tab").addEventListener("click", function () { // When user clicks the section "Categories" on modal it loads the name of the categories 
    loadCategories();
  });
  loadEditForm();
}

function insert() {
  let form = new FormData($("#form")[0]);
  $.ajax({
    url: "api/articles",
    type: "POST",
    data: form,
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
        url: `api/articles?id=${id}`,
        type: "DELETE",
        success: function (result) {
          toastr.success(result.message);
          getArticles();
        },
      });
    }
  });
}
function edit() {
  let form = new FormData($("#edit-form")[0]);
  $.ajax({
    url: "api/articles",
    type: "PUT",
    data: form,
    success: function (result) {
      getSuccessResponse(result);
    },
    error: function (xhr) {
      getErrorResponse(xhr);
    },
  });
}

function getArticles() {
  $("#tableArticles").DataTable({
    ...getSettingsDataTable(),
    buttons: getButtonsDataTable(),
    dom: getDomStyleDataTable(),
    ajax: {
      url: "api/articles",
      type: "get",
      dataType: "json",
    },
    columns: [
      { data: "id_article" },
      { data: "name" },
      { data: "code" },
      { data: "description" },
      { data: "id_category" },
      { data: "image" },
      { data: "stock" },
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
  $('#tableArticles').on('click', '.edit-button', function () {
    let row = $(this).closest('tr');
    let table = $('#tableArticles').DataTable();
    if (row.hasClass('child')) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    $("#edit-name").val(data.name);
    $("#edit-idarticle").val(data.id_article);
    $("#edit-description").text(data.description);
    data.active === "0" ? $("#customSwitch1").prop("checked", false) : $("#customSwitch1").prop("checked", true);
  });
}

function getCategories() {
  return $.ajax({
    url: "api/categories?getCategoriesName",
    type: "GET",
    dataType: "json"
  });

}


function loadCategories() {
  if (document.getElementById("categorySelect").children.length <= 0) {
    getCategories()
      .done(function (result) {
        for (let index = 0; index < result["data"].length; index++) {
          let option = document.createElement("option");
          option.text = result["data"][index].NAME;
          let select = document.getElementById("categorySelect");
          select.appendChild(option);
        }
      })
      .fail(function (result) {
        getErrorResponse(result);
      });


  }
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
    getArticles();
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
