$("#changePassForm").on("submit", async function(e) {
    e.preventDefault();

    const errorBox = $('#errorBox');
    errorBox.hide();

    const targetId = $("#targetUserId").val() || null;
    const oldPassword = $("#old_password").val();
    const newPassword = $("#new_password").val();
    const confirmPassword = $("#confirm_password").val();

    if (newPassword != confirmPassword) {
        errorBox.text("Passwords do not match").show();
        return;
    }

    try {
        const res = await fetch("../api/accountHandler.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "changePass",
                data: {
                    user_id: targetId,
                    old_password: oldPassword,
                    new_password: newPassword
                }
            })
        });

        const result = await res.json();
        if (res.ok) {
            alert("Password updated successfully!");
            window.location.href = "index.php";
        } else {
            errorBox.text(result.message).show();
        }
    } catch (err) {
        errorBox.text("An unexpected error has occurred. Full error message is in the web console.").show();
        console.error(err)
    }
});