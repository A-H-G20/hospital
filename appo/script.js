// script.js

document.getElementById("appointmentForm").onsubmit = function(event) {
    const fields = ["name", "email", "phone", "age", "gender", "disease", "doctor", "date", "time"];
    for (const field of fields) {
        const value = document.getElementById(field).value;
        if (!value) {
            alert("Please complete all fields!");
            event.preventDefault();
            break;
        }
    }
};

document.getElementById("appointmentForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    // Use fetch API to send form data to PHP in the background
    fetch("appointment.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === "success") {
            // Show the success box with an animation
            const successBox = document.getElementById("successBox");
            successBox.classList.add("show");

            // Reset the form fields
            document.getElementById("appointmentForm").reset();

            // Hide the success box after 3 seconds
            setTimeout(() => {
                successBox.classList.remove("show");
            }, 3000);
        } else {
            console.error("Form submission failed:", result);
        }
    })
    .catch(error => console.error("Error:", error));
});

