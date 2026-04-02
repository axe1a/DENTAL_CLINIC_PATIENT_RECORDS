const errorBox = $('#errorBox');

$("#manageUserForm").on("submit", async function(e) {
    e.preventDefault();
    errorBox.hide();

    const targetId = $("#targetUserId").val() || null;
    const username = $("#username").val();
    const password = $("#password").val();
    const confirmPassword = $("#confirm_password").val();
    const action = targetId ? "changePass" : "addUser";

    if (password !== confirmPassword) {
        errorBox.text("Passwords do not match!").show();
        return;
    }

    const payloadData = targetId 
        ? { user_id: targetId, new_password: password } 
        : { username: username, password: password };

    const payload = {
        action: action,
        data: payloadData
    };

    try {
        const response = await fetch("../api/accountHandler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            window.location.href = "personnel_list.php";
        } else {
            errorBox.text(result.message).show();
        }
    } catch (err) {
        errorBox.text("An unexpected error has occurred. Full error message is in the web console.").show();
        console.error(err)
    }
});

async function deleteUser(targetId) {
    errorBox.hide();
    if(!confirm ("Delete this personnel's account?")) return;

    try {
        const response = await fetch("../api/accountHandler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "deleteUser",
                data: {
                    user_id: targetId
                }
            })
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            window.location.href = "personnel_list.php";
        } else {
            errorBox.text(result.message).show();
        }
    } catch (err) {
        errorBox.text("An unexpected error has occurred. Full error message is in the web console.").show();
        console.error(err)
    }
}