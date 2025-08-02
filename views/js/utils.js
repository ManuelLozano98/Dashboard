function getDeleteMsg() {
    return Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it"
    });
}

function getButtonsDataTable() {
    return [
        { extend: "copy", text: "Copy" },
        { extend: "csv", text: "CSV" },
        { extend: "excel", text: "Excel" },
        { extend: "pdf", text: "PDF" },
        { extend: "colvis", text: "Column visibility" },
    ];
}
function getDomStyleDataTable() {
    return "<'row'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" + // Up: Select + Buttons + Search
        "<'row'<'col-md-12'tr>>" + // Table
        "<'row'<'col-md-6'i><'col-md-6'p>>"; // Down: Info + Pagination
}
function getActionsColumnDataTable() {
    return {
        data: null, // Column generated manually
        title: "Actions",
        orderable: false, // This column does not allow sorting
        searchable: false, // This column does not allow searching
        render: function (data, type, row) {
            let id = Object.values(data)[0];
            return `
            <button id="btn-edit${id}" class="btn btn-success btn-sm rounded-0 edit-button" type="button" data-toggle="modal" data-target="#modal-edit-default" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
            <button id="btn-delete_${id}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>
          `;
        },
    }
}

function getSettingsDataTable() {
    return {
        bDestroy: true,
        iDisplayLength: 10,
        order: [[0, "desc"]],
        autoWidth: false,
        responsive: true,
    };
}

function closeModalDialog() {
    $(document).on('keydown', function (event) {
        if (event.key === "Escape") {
            $('.modal').modal('hide');
        }
    });
}

function updateCounter(elementId, counterId) {
    const limit = 255;
    $("#" + counterId).text($("#" + elementId).val().length + "/" + limit);
    if ($("#" + elementId).val().length >= limit) {
        $("#" + counterId).addClass("text-danger", "fw-bold");
    } else {
        $("#" + counterId).removeClass("text-danger", "fw-bold");
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

function loadButtonsAction() {
    document.addEventListener("click", function (e) {
        let deleteBtn = e.target.closest('[id^="btn-delete_"]');
        let editBtn = e.target.closest('#edit-save');
        let addBtn = e.target.closest('#save');

        if (deleteBtn) {
            let id = deleteBtn.id.split("_")[1];
            deleteItem(id);
        }

        if (editBtn) {
            edit();
        }
        if (addBtn) {
            insert();
        }
    });
}

function loadCounter() {
    document.getElementById("description").addEventListener("input", function (event) {
        updateCounter(this.id, "counter");
    });
    document.getElementById("edit-description").addEventListener("input", function (event) {
        updateCounter(this.id, "edit-counter");
    });
}

function getSuccessResponse(response, fn) {
    if (response.status >= 400) { // >= 400 Error codes
        if (response.details) {
            response.message += response.details.map((element) => {
                return `<br> ${Object.keys(element)} => ${Object.values(element)}`;
            });
        }
        toastr.warning(response.message);
    } else {
        toastr.success(response.message);
        fn();
    }
}

function loginInvalid() {
    return Swal.fire({
        title: "Invalid login",
        text: "Incorrect username or password",
        icon: "error",
        confirmButtonColor: "#3085d6",
        showCloseButton: true,
    });
}


closeModalDialog();



