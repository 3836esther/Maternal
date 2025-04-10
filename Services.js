document.addEventListener("DOMContentLoaded", function () {
    const appointmentForm = document.getElementById("appointment-form");

    appointmentForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent form from refreshing the page

        // Get form values
        const fullName = document.getElementById("full-name").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;
        const service = document.getElementById("service").value;
        const doctor = document.getElementById("doctor").value;
        const checkupType = document.getElementById("checkup-type").value;
        const appointmentDate = document.getElementById("appointment-date").value;

        // Validate if the form fields are empty
        if (!fullName || !email || !phone || !service || !doctor || !checkupType || !appointmentDate) {
            alert("All fields are required.");
            return;
        }

        // Create a FormData object
        let formData = new FormData();
        formData.append("full_name", fullName);
        formData.append("email", email);
        formData.append("phone", phone);
        formData.append("service", service);
        formData.append("doctor", doctor);
        formData.append("checkup_type", checkupType);
        formData.append("appointment_date", appointmentDate);

        // Send form data to services.php to store the appointment
        fetch("services.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json()) // Expect JSON response
        .then(data => {
            if (data.status === "success") {
                alert(data.message); // Show success message
                // Optionally, you can redirect or clear the form
                appointmentForm.reset();
            } else {
                alert(data.message); // Show error message
            }
        })
        .catch(error => console.error("Error:", error)); // Handle network errors
    });
});
