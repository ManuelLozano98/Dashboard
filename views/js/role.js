/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function init() {
    loadTableRoles();
    loadButtonsAction();
    loadUsersRoles("users");
    loadEditForm();
    loadTableUsersRoles();
    loadUsersRoles("users-roles");
    loadRoles("roles-users");
    loadRoles("roles-users2");
    loadButtonsUsersRoles();
}

function loadButtonsUsersRoles() {
    document.addEventListener("click", function (e) {
        let deleteBtn = e.target.closest('[id^="btn_delete_"]');
        let addActionBtn = e.target.closest('[id^="btn_add_"]');
        let addBtn = e.target.closest('#saveUser');
        let saveBtn = e.target.closest('#saveRole');
        let deleteAllBtn = e.target.closest('[id^="btn-deleteAll_"]');

        if (deleteBtn) {
            let idRole = deleteBtn.getAttribute("data-id_role");
            let idUser = deleteBtn.closest('tr').cells[0].firstChild.getAttribute("data-id_user");
            deleteUsersRoles(idRole, idUser);
        }

        if (addActionBtn) {
            getRoles().
                done(function (response) {
                    let rolesButtons = addActionBtn.closest('tr').cells[1].children;
                    let roles = [];
                    for (let i = 0; i < rolesButtons.length; i++) {
                        roles[i] = rolesButtons[i].innerText;
                    }
                    let data = response.data.filter(x => !roles.includes(x.name));
                    let select = $("#roles-users2");

                    select.empty();

                    data.forEach(item => {
                        const option = new Option(item.name, item.id);
                        select.append(option);
                    });
                    select.select2();
                    const userId = addActionBtn.id.split("_")[2];
                    $("#thisUser").val(userId);

                });



        }
        if (saveBtn) {
            let userId = $("#thisUser").val();
            insertRolesToUser(userId);
        }
        if (addBtn) {
            insertUsersRoles();
        }
        if (deleteAllBtn) {
            const id = deleteAllBtn.id.split("_")[1];
            deleteAllRoles(id);
        }
    });

}
function deleteAllRoles(id) {
    console.log(id);
    getDeleteMsg().then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `api/users/${id}/roles`,
                type: "DELETE",
                dataType: "json",
                success: function (result) {
                    getSuccessResponse(result, loadTableUsersRoles);
                },
                error: function (xhr) {
                    getErrorResponse(xhr);
                },
            });
        }
    });
}

function insertRolesToUser(userId) {
    let rolesId = $("#roles-users2").val();
    let lastRequest = 0;
    for (let i = 0; i < rolesId.length; i++) {
        lastRequest++;
        $.ajax({
            url: `api/users/${userId}/roles`,
            type: "POST",
            data: JSON.stringify({
                id_role: rolesId[i]
            }),
            dataType: "json",
            success: function (response) {
                if (rolesId.length === lastRequest) {
                    getSuccessResponse(response, loadTableUsersRoles);
                }
            },
            error: function (xhr) {
                if (rolesId.length === lastRequest) {
                    getErrorResponse(xhr);
                }
            },
        });
    }

}

function insertUsersRoles() {
    let usersId = $("#users-roles").val();
    let rolesId = $("#roles-users").val();
    console.log(rolesId);
    let lastRequest = 0;
    for (let i = 0; i < usersId.length; i++) {
        for (let x = 0; x < rolesId.length && i !== usersId.length; x++) {
            let obj = {
                id_user: usersId[i],
                id_role: rolesId[x]
            };
            lastRequest++;
            let currentRequest = lastRequest;

            $.ajax({
                url: `api/users/${obj.id_user}/roles`,
                type: "POST",
                data: JSON.stringify({
                    id_role: obj.id_role
                }),
                dataType: "json",
                success: function (response) {
                    if (currentRequest === lastRequest) {
                        getSuccessResponse(response, loadTableUsersRoles);
                        loadTableRoles();
                    }
                },
                error: function (xhr) {
                    if (currentRequest === lastRequest) {
                        getErrorResponse(xhr);
                    }
                },
            });
        }
    }



}
function deleteUsersRoles(idRole, idUser) {
    getDeleteMsg().then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `api/users/${idUser}/roles/${idRole}`,
                type: "DELETE",
                dataType: "json",
                success: function (result) {
                    getSuccessResponse(result, loadTableUsersRoles);
                },
                error: function (xhr) {
                    getErrorResponse(xhr);
                },
            });
        }
    });

}

function insert() {
    let role = {
        name: $("#name").val(),
        users: $("#users").val(),
    };
    $.ajax({
        url: "api/roles",
        type: "POST",
        data: JSON.stringify(role),
        dataType: "json",
        success: function (response) {
            getSuccessResponse(response, loadTableRoles);
            loadTableUsersRoles();
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
                url: `api/roles/${id}`,
                type: "DELETE",
                dataType: "json",
                success: function (result) {
                    getSuccessResponse(result, loadTableRoles);
                    loadTableUsersRoles();
                },
                error: function (xhr) {
                    getErrorResponse(xhr);
                },
            });
        }
    });
}
function edit() {
    let id = $("#edit-idrole").val();
    let role = {
        name: $("#edit-name").val(),
    };
    $.ajax({
        url: `api/roles/${id}`,
        type: "PUT",
        data: JSON.stringify(role),
        dataType: "json",
        success: function (result) {
            getSuccessResponse(result, loadTableRoles);
            loadTableUsersRoles();
        },
        error: function (xhr) {
            getErrorResponse(xhr);
        },
    });
}

function loadTableRoles() {
    getRoles().
        done(function (response) {
            const data = response.data;
            if (!data || data.length === 0) return;
            const columns = Object.keys(data[0]).map(key => {
                return { data: key };
            });
            columns[columns.length] = getActionsColumnDataTable();
            $('#tableRoles').DataTable({
                data: data,
                columns: columns,
                ...getSettingsDataTable(),
                buttons: getButtonsDataTable(),
                dom: getDomStyleDataTable(),
            });


        });
}

function getRoles() {
    return $.ajax({
        url: "api/roles",
        type: "GET",
        dataType: "json",
    });
}

function getUsersRoles() {
    return $.ajax({
        url: "api/users/roles",
        type: "GET",
        dataType: "json",
    });


}

function loadTableUsersRoles() {
    getUsersRoles().
        done(function (response) {
            const data = response.data;
            console.log(data);
            if (!data || data.length === 0) return;

            let columnsData = [
                {
                    data: "username",
                    render: function (data, type, row) {
                        return `<span data-id_user="${row.id_user}">${data}</span>`;
                    }
                },
                {
                    data: "roles",
                    render: function (data, type, row) {
                        return data.map(role => {
                            return `
            <span class="badge bg-primary roles" data-id_role="${role.id_role}" title="Remove ${role.name}" id="btn_delete_${role.id}">
              ${role.name}
              <input type="hidden" value="${role.id}"/>
            </span>
          `;
                        });
                    }
                },
                {
                    data: null, // Column generated manually
                    orderable: false, // This column does not allow sorting
                    searchable: false, // This column does not allow searching
                    render: function (data, type, row) {
                        let id = Object.values(data)[0];
                        return `
            <button id="btn_add_${id}" class="btn btn-success btn-sm rounded-0 add-button" type="button" data-toggle="modal" data-target="#modal-add-default" data-placement="top" title="Add"><i class="fa fa-plus-circle"></i></button>
            <button id="btn-deleteAll_${id}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>
          `;
                    },
                }
            ];


            $('#tableUsersRoles').DataTable({
                data: data,
                columns: columnsData,
                ...getSettingsDataTable(),
                buttons: getButtonsDataTable(),
                dom: getDomStyleDataTable(),
            });


        });

}

function loadEditForm() {
    $('#tableRoles').on('click', '.edit-button', function () {
        let row = $(this).closest('tr');
        let table = $('#tableRoles').DataTable();
        if (row.hasClass('child')) {
            row = row.prev(); // needed for responsive tables
        }
        let data = table.row(row).data();
        $("#edit-idrole").val(data.id);
        $("#edit-name").val(data.name);
    });
}

function getUsers() {
    return $.ajax({
        url: "api/users/username",
        type: "GET",
        dataType: "json"
    });

}


function loadUsersRoles(selectHtml) {
    if (document.getElementById(selectHtml).children.length == 0) {
        getUsers()
            .done(function (result) {
                let select = document.getElementById(selectHtml);
                for (let index = 0; index < result["data"].length; index++) {
                    let option = document.createElement("option");
                    option.text = result["data"][index].username;
                    option.value = result["data"][index].id;
                    select.appendChild(option);
                }
            })
            .fail(function (result) {
                getErrorResponse(result);
            });
    }
}
function loadRoles(selectHtml) {
    if (document.getElementById(selectHtml).children.length == 0) {
        getRoles()
            .done(function (result) {
                let select = document.getElementById(selectHtml);
                for (let index = 0; index < result["data"].length; index++) {
                    let option = document.createElement("option");
                    option.text = result["data"][index].name;
                    option.value = result["data"][index].id;
                    select.appendChild(option);
                }
            })
            .fail(function (result) {
                getErrorResponse(result);
            });
    }
}





init();
