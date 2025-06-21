/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getArticles();
  document.getElementById("tab2-tab").addEventListener("click", function () { // When user clicks the section "Categories" on modal it loads the name of the categories 
<<<<<<< HEAD
    loadCategories();
  });
  document.getElementById("code").addEventListener("input", debounce(function () {
    validateCode(this.value);
  }, 500)
  );
  document.getElementById("generate-code").addEventListener("click", function (event) {
    event.preventDefault();
    generateCode();
  })
  loadEditForm();
=======
    loadCategories("categorySelect");
  });
  loadButtonsAction();
  loadCounter();
  validateCodeByUser("code", "barcode"); // Validate code when user tries to add an article
  setupGenerateCode("generate-code", "barcode", "code");
  loadEditForm();
  setupGenerateCode("edit-generate-code", "edit-barcode", "edit-code");
  validateCodeByUser("edit-code", "edit-barcode"); // Validate code when user tries to edit an article
}

function validateCodeByUser(codeId, barcodeId) {
  document.getElementById(codeId).addEventListener("input", debounce(function () {
    validateCode(barcodeId, this.value, codeId);
  }, 500)
  );
}
function setupGenerateCode(buttonId, barcodeId, codeId) {
  document.getElementById(buttonId).addEventListener("click", function (event) {
    event.preventDefault();
    generateCode(barcodeId, codeId);
  });
>>>>>>> develop
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
<<<<<<< HEAD
      getSuccessResponse(response);
=======
      getSuccessResponse(response, getArticles);
>>>>>>> develop
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
<<<<<<< HEAD
=======
        error: function (xhr) {
          getErrorResponse(xhr);
        },
>>>>>>> develop
      });
    }
  });
}
function edit() {
<<<<<<< HEAD
  let form = new FormData($("#edit-form")[0]);
  $.ajax({
    url: "api/articles",
    type: "PUT",
    data: form,
    success: function (result) {
      getSuccessResponse(result);
=======
  let form = new FormData($("#form-edit")[0]);
  form.append("id_article", $("#articleId").val());
  form.append("_method", "PUT");
  form.append("edit-active", $("#customSwitch1").is(":checked") === true ? 1 : 0);
  $.ajax({
    url: "api/articles",
    type: "POST",
    data: form,
    processData: false,
    contentType: false,
    success: function (result) {
      getSuccessResponse(result, getArticles);
>>>>>>> develop
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
<<<<<<< HEAD
      { data: "image" },
=======
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
>>>>>>> develop
      { data: "stock" },
      { data: "active" },
      getActionsColumnDataTable(),
    ]
  });
}
<<<<<<< HEAD
function updateCounter() {
  const limit = 255;
  $("#counter").text($("#description").val().length + "/" + limit);
  if ($("#description").val().length >= limit) {
    $("#counter").addClass("text-danger", "fw-bold");
  } else {
    $("#counter").removeClass("text-danger", "fw-bold");
  }
}
=======
>>>>>>> develop

function loadEditForm() {
  $('#tableArticles').on('click', '.edit-button', function () {
    let row = $(this).closest('tr');
    let table = $('#tableArticles').DataTable();
    if (row.hasClass('child')) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
<<<<<<< HEAD
    $("#edit-name").val(data.name);
    $("#edit-idarticle").val(data.id_article);
    $("#edit-description").text(data.description);
=======
    $("#articleId").val(data.id_article);
    $("#edit-name").val(data.name);
    $("#edit-idarticle").val(data.id_article);
    $("#edit-description").val(data.description);
    updateCounter("edit-description", "edit-counter");
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
>>>>>>> develop
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


<<<<<<< HEAD
function loadCategories() {
  if (document.getElementById("categorySelect").children.length <= 0) {
    getCategories()
      .done(function (result) {
=======
function loadCategories(selectHtml) {
  if (document.getElementById(selectHtml).children.length <= 0) {
    getCategories()
      .done(function (result) {
        let select = document.getElementById(selectHtml);
>>>>>>> develop
        for (let index = 0; index < result["data"].length; index++) {
          let option = document.createElement("option");
          option.text = result["data"][index].NAME;
          option.value = result["data"][index].ID_CATEGORY;
<<<<<<< HEAD
          let select = document.getElementById("categorySelect");
=======
>>>>>>> develop
          select.appendChild(option);
        }
      })
      .fail(function (result) {
        getErrorResponse(result);
      });


  }
}
<<<<<<< HEAD
function generateCode() {
  let code = document.getElementById("code");
  code.value = Date.now() + Math.floor(Math.random());
  validateCode(code.value);
}

function validateCode(code) {
  $.ajax({  
=======
function generateCode(element, elementId) {
  let code = document.getElementById(elementId);
  code.value = Date.now() + Math.floor(Math.random());
  validateCode(element, code.value, elementId);
}

function generateBarCode(element, code) {
  JsBarcode(element, code);
  $(element).attr("width", "200px");
}

function validateCode(element, code, inputId) {
  $.ajax({
>>>>>>> develop
    url: `api/articles?code=${code}`,
    type: "GET",
    dataType: "json"
  }).done(function (result) {
    if (result.error === "OK") {
<<<<<<< HEAD
      $("#code").removeClass("is-invalid");
      $("#code").addClass("is-valid");
    }
    else {
      $("#code").removeClass("is-valid");
      $("#code").addClass("is-invalid");
=======
      $("#" + inputId).removeClass("is-invalid");
      $("#" + inputId).addClass("is-valid");
      generateBarCode("#" + element, code);
    }
    else {
      $("#" + inputId).removeClass("is-valid");
      $("#" + inputId).addClass("is-invalid");
>>>>>>> develop
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

<<<<<<< HEAD
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

=======
>>>>>>> develop
init();
