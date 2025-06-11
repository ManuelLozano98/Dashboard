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
            <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete" onclick="deleteItem(${id})"><i class="fa fa-trash"></i></button>
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

closeModalDialog();



