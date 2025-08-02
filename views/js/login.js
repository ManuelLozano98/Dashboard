$("#loginForm").submit(function (e) {
    e.preventDefault();
    username = $("#username").val();
    password = $("#password").val();
    $.post("api/users?q=login", {
        login: username,
        password: password
    }, function (data) {
        if (data.error === "Invalid credentials") {
            loginInvalid();
        }
        if (data.message === "Login successful") $(location).attr("href", "home");

    }, "json")
        .fail(function (xhr) {
            getErrorResponse(xhr);
        })
});