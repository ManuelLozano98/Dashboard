/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getArticles();
  document.getElementById("tab2-tab").addEventListener("click", function () { // When user clicks the section "Categories" on modal it loads the name of the categories 
    loadCategories("categorySelect");
  });
  validateCodeByUser("code", "barcode"); // Validate code when user tries to add an article
  setupGenerateCode("generate-code", "barcode", "code");
  loadEditForm();
  setupGenerateCode("edit-generate-code", "edit-barcode", "edit-code");
  validateCodeByUser("edit-code", "edit-barcode"); // Validate code when user tries to edit an article
}

function validateCodeByUser(codeId, barcodeId) {
  document.getElementById(codeId).addEventListener("input", debounce(function () {
    validateCode(barcodeId, this.value);
  }, 500)
  );
}
function setupGenerateCode(buttonId, barcodeId, codeId) {
  document.getElementById(buttonId).addEventListener("click", function (event) {
    event.preventDefault();
    generateCode(barcodeId, codeId);
  });
}

function insert() {
  let form = new FormData($("#form")[0]);
  $.ajax({
    url: "api/articles",
    type: "POST",
    data: form,
    processData: false,
    contentType: false,
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
      {
        data: "image",
        render: function (data) {
          let result = "No image found";
          if (data !== "") {
            result = `<img alt="${data.substring(0, data.indexOf("."))}" src="articles_img/${data}" width="100px" height="100px"/>`;
          }
          return result;
        }
      },
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
    $("#edit-stock").val(data.stock);
    $("#edit-code").val(data.code);
    generateBarCode("#edit-barcode", data.code);
    loadCategories("edit-categorySelect");
    for (let element of document.getElementById("edit-categorySelect").children) { // Find the category to which the article belongs
      element.removeAttribute("selected");
      if (data.id_category === element.value) {
        element.setAttribute("selected", true);
      }
    }
    if (data.image !== "") { // If the image exists will show up
      $("#edit-img").show();
      $("#edit-img").attr("src", "articles_img/" + data.image);
    }
    else { // If the image does not exists will hide
      $("#edit-img").hide();
    }

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


function loadCategories(selectHtml) {
  if (document.getElementById(selectHtml).children.length <= 0) {
    getCategories()
      .done(function (result) {
        for (let index = 0; index < result["data"].length; index++) {
          let option = document.createElement("option");
          option.text = result["data"][index].NAME;
          option.value = result["data"][index].ID_CATEGORY;
          let select = document.getElementById(selectHtml);
          select.appendChild(option);
        }
      })
      .fail(function (result) {
        getErrorResponse(result);
      });


  }
}
function generateCode(element, elementId) {
  let code = document.getElementById(elementId);
  code.value = Date.now() + Math.floor(Math.random());
  validateCode(element, code.value);
}

function generateBarCode(element, code) {
  JsBarcode(element, code);
  $(element).attr("width", "200px");
}

function validateCode(element, code) {
  $.ajax({
    url: `api/articles?code=${code}`,
    type: "GET",
    dataType: "json"
  }).done(function (result) {
    if (result.error === "OK") {
      $("#code").removeClass("is-invalid");
      $("#code").addClass("is-valid");
      generateBarCode("#" + element, code);
    }
    else {
      $("#code").removeClass("is-valid");
      $("#code").addClass("is-invalid");
    }
  });
}

function debounce(func, delay) {
  let timeout;
  return function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, arguments), delay);
  };
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
