$("#addPatientWizardForm").on("submit", async function(event){
    event.preventDefault();

    const data = {};
    $(this).serializeArray().forEach(function(field){
        if(data[field.name]){
            if(!Array.isArray(data[field.name])){
                data[field.name] = [data[field.name]];
            }
            data[field.name].push(field.value);
        } else {
            data[field.name] = field.value;
        }
    });

    try {
        const response = await fetch("api/patientRecordHandler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "addPatientRecord",
                data: data
            })
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            window.location.href = "index.php";
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err)
    }
});