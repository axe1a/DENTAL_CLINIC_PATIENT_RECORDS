$("#loginButton").on("click", async function(event){
    event.preventDefault();

    const usernameVal = $("#usernameField").val();
    const passwordVal = $("#passwordField").val();

    if(usernameVal == "" || passwordVal == "") return;

    try {
        const response = await fetch("./api/accountHandler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                "action": "loginUser",
                "data": {
                    "username": usernameVal,
                    "password": passwordVal
                }
            })
        });

        const result = await response.json();
        if(!response.ok){
            alert(result.message);
            return;
        }

        // After successful login, go to dashboard.
        window.location.href = "dashboard/index.php";
    } catch(error) {
        // TODO: Notify user about error (perhaps incorrect password or username does not exist)
        console.log(error);
    }
});