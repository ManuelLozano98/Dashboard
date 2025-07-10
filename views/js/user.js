/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
  getUsers();
  loadButtonsAction();
  setup();
  document.getElementById("tab2-tab").addEventListener("click", function () {
    loadDocumentTypes("document_type");
  });
  loadEditForm();
  verify();
}

function setup() {
  $("#eyepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("password");
  });
  $("#eyerepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("repassword");
  });

  $("#edit-eyepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("edit-password");
  });
  $("#edit-eyerepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("edit-repassword");
  });

}


function insert() {
  if (isFormValid($("#form"))) {
    let form = new FormData($("#form")[0]);
    $.ajax({
      url: "api/users",
      type: "POST",
      data: form,
      processData: false,
      contentType: false,
      success: function (response) {
        getSuccessResponse(response, getUsers);
      },
      error: function (xhr) {
        getErrorResponse(xhr);
      },
    });
  }
  else {
    redirectTab("#form");
  }
}



function redirectTab(formSelector) {
  let tabError = $(formSelector).find('.is-invalid').first().closest('.tab-pane');
  let userTab = $(formSelector + ' .tab-pane.fade.active.show');
  if (!tabError.is(userTab)) {
    let tabId = tabError.attr('id');
    let formTabs = formSelector.substr(-4) === "edit" ? "#edit-formTabs" : "#formTabs"
    let linkUserTab = $(formTabs).find('.nav-link.active');
    linkUserTab.removeClass("active");
    linkUserTab.removeAttr("aria-selected");
    let linkErrorTab = $('#' + tabId + "-tab");
    linkErrorTab.addClass("active");
    linkUserTab.attr("aria-selected", "true");
    userTab.removeClass("active show");
    tabError.addClass("active show");
  }
}
function deleteItem(id) {
  getDeleteMsg().then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: `api/users?id=${id}`,
        type: "DELETE",
        success: function (result) {
          toastr.success(result.message);
          getUsers();
        },
        error: function (xhr) {
          getErrorResponse(xhr);
        },
      });
    }
  });
}
function edit() {
  if (isFormValid($('#form-edit'))) {
    let form = new FormData($("#form-edit")[0]);
    form.append("id_user", $("#id_user").val());
    form.append("_method", "PUT");
    form.append("active", $("#customSwitch1").is(":checked") === true ? 1 : 0);
    $.ajax({
      url: "api/users",
      type: "POST",
      data: form,
      processData: false,
      contentType: false,
      success: function (result) {
        getSuccessResponse(result, getUsers);
      },
      error: function (xhr) {
        getErrorResponse(xhr);
      },
    });
  }
  else {
    redirectTab("#form-edit");
  }
}

function getUsers() {
  $.ajax({
    url: "api/users",
    type: "GET",
    dataType: "json",
    success: function (response) {
      const data = response.data;
      if (!data || data.length === 0) return;
      const columns = Object.keys(data[0]).map(key => {
        if (key === "IMAGE") {
          return {
            data: key,
            render: function (data) {
              return data
                ? `<img alt="${data}" src="files/users_img/${data}" width="100px" height="100px"/>`
                : "No image";
            }
          };
        }
        if (key === "ACTIVE") {
          return {
            data: key,
            render: function (data) {
              return data === "0" ? "User not verified / activated" : "Activated";

            }
          };
        }
        return { data: key };
      });
      columns[columns.length] = getActionsColumnDataTable();
      $('#tableUsers').DataTable({
        data: data,
        columns: columns,
        ...getSettingsDataTable(),
        buttons: getButtonsDataTable(),
        dom: getDomStyleDataTable(),
      });
    }
  });
}

function loadEditForm() {
  $('#tableUsers').on('click', '.edit-button', function () {
    let row = $(this).closest('tr');
    let table = $('#tableUsers').DataTable();
    if (row.hasClass('child')) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    console.log(data);
    $("#id_user").val(data.ID_USER);
    $("#edit-name").val(data.NAME);
    $("#edit-username").val(data.USERNAME);
    $("#edit-phone").val(data.PHONE);
    $("#edit-address").val(data.ADDRESS);
    $("#edit-email").val(data.EMAIL);
    $("#edit-document").val(data.DOCUMENT);
    loadDocumentTypes("edit-document_type");
    for (let element of document.getElementById("edit-document_type").children) { // Find the document
      element.removeAttribute("selected");
      if (data.ID_CATEGORY === element.value) {
        element.setAttribute("selected", true);
      }
    }
    if (data.IMAGE !== "") { // If the image exists will show up
      $("#edit-img").show();
      $("#edit-img").attr("src", "files/users_img/" + data.IMAGE);
    }
    else { // If the image does not exists will hide
      $("#edit-img").hide();
    }
    data.ACTIVE === "0" ? $("#customSwitch1").prop("checked", false) : $("#customSwitch1").prop("checked", true);
  });
}

function getDocumentTypes() {
  return $.ajax({
    url: "api/users?getDocumentTypes",
    type: "GET",
    dataType: "json"
  });

}


function loadDocumentTypes(selectHtml) {
  if (document.getElementById(selectHtml).children.length <= 0) {
    getDocumentTypes()
      .done(function (result) {
        let select = document.getElementById(selectHtml);
        for (let index = 0; index < result["data"].length; index++) {
          let option = document.createElement("option");
          option.text = result["data"][index].NAME;
          option.value = result["data"][index].ID_DOCUMENT_TYPE;
          select.appendChild(option);
        }
      })
      .fail(function (result) {
        getErrorResponse(result);
      });


  }
}

function verify() {
  $.validator.addMethod("usernameValidator", function (value, element) {
    return /^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/.test(value);
  });

  $.validator.addMethod("nameValidator", function (value, element) {
    return /^[a-zA-Z]{3,}$/.test(value);
  });

  $.validator.addMethod("emailValidator", function (value, element) {
    return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
  });

  $.validator.addMethod("passwordValidator", function (value, element) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value);
  });

  $.validator.addMethod("checkPasswordsValidator", function (value, element, param) {
    let passwordValue = $(param).val();
    return value === passwordValue;
  });
  $.validator.addMethod("phoneValidator", function (value, element) {
    return this.optional(element) || /^[6-9]\d{8}$/.test(value);
  });
  $.validator.addMethod("dniValidator", function (value, element) {
    let array = ['T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E'];
    return this.optional(element) || /^\d{8}[A-HJ-NP-TV-Z]$/i.test(value) && value.toUpperCase() == value.substring(0, value.length - 1) + array[value.substring(0, value.length - 1) % 23];
  });

  $.validator.addMethod("editEmailValidator", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
  });

  $.validator.addMethod("editPasswordValidator", function (value, element, params) {
    let param = $(params).val();
    let flag = 0;
    if (value === "" && param === "") flag = 1;
    if (value !== "" && param !== "") flag = 2;
    if (flag === 1) return true;
    if (flag === 2) return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value);
    return false;
  });

  let commonRules = {
    name: {
      required: true,
      minlength: 3,
      nameValidator: true
    },
    phone: {
      phoneValidator: true
    },
    document: {
      dniValidator: true
    }
  };

  let commonMessages = {
    name: {
      required: "Please enter a name",
      minlength: "Please enter a valid name, 3 characters at least",
      nameValidator: "Please enter a valid name"
    },
    phone: {
      phoneValidator: "Please enter a valid phone"
    },
    document: {
      dniValidator: "Please enter a valid DNI"
    }
  };

  let validationConfig = {
    ignore: [],
    errorElement: 'span',
    errorPlacement: (error, element) => {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: (element) => {
      $(element).addClass('is-invalid');
    },
    unhighlight: (element) => {
      $(element).removeClass('is-invalid');
    }
  };


  $('#form').validate({
    ...validationConfig,
    rules: {
      ...commonRules,
      username: {
        required: true,
        minlength: 4,
        usernameValidator: true
      },
      email: {
        required: true,
        emailValidator: true,
      },
      password: {
        required: true,
        passwordValidator: true,
      },
      repassword: {
        required: true,
        passwordValidator: true,
        checkPasswordsValidator: "#password",
      }
    },
    messages: {
      ...commonMessages,
      email: {
        required: "Please enter a email address",
        emailValidator: "Please enter a valid email address"
      },
      username: {
        required: "Please enter a username",
        usernameValidator: "The username must be between 4 and 20 characters, with no special characters or spaces, and not begin or end with . or _"
      },
      password: {
        required: "Please provide a password",
        passwordValidator: "Your password must be at least 8 characters long, 1 uppercase, 1 lowercase, 1 number and 1 symbol"
      },
      repassword: {
        required: "Please provide a password",
        passwordValidator: "Your password must be at least 8 characters long, 1 uppercase, 1 lowercase, 1 number and 1 symbol",
        checkPasswordsValidator: "The password doesn't match"
      }
    }
  });

  $('#form-edit').validate({
    ...validationConfig,
    rules: {
      ...commonRules,

      email: {
        editEmailValidator: true,
      },
      password: {
        editPasswordValidator: "#edit-repassword",
      },
      repassword: {
        editPasswordValidator: "#edit-password",
        checkPasswordsValidator: "#edit-password",
      }
    },
    messages: {
      ...commonMessages,
      email: {
        editEmailValidator: "Please enter a valid email address"
      },
      password: {
        editPasswordValidator: "Your password must be at least 8 characters long, 1 uppercase, 1 lowercase, 1 number and 1 symbol",
      },
      repassword: {
        editPasswordValidator: "",
        checkPasswordsValidator: "The password doesn't match"
      },
    }
  });


}


function toggleIcon(elementId) {
  if ($("#" + elementId).attr("class") === "fas fa-eye") {
    $("#" + elementId).attr("class", "fas fa-eye-slash");
  }
  else {
    $("#" + elementId).attr("class", "fas fa-eye");
  }

}
function toggleInput(elementId) {
  if ($("#" + elementId).attr("type") === "password") {
    $("#" + elementId).attr("type", "text");
  }
  else {
    $("#" + elementId).attr("type", "password");
  }
}

function isFormValid(formSelector) {
  return $(formSelector).valid();
}



init();
