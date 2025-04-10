document.addEventListener("DOMContentLoaded", function () {
    // Fetch user profile on load
    fetch('profile.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const user = data.user;
                document.getElementById('profile-username').value = user.username;
                document.getElementById('profile-email').value = user.email;
                document.getElementById('profile-phone').value = user.phone;
                document.getElementById('profile-medical-history').value = user.medical_history;
            } else {
                alert("Failed to load profile.");
            }
        });

    // Handle profile update
    document.getElementById('profile-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
        });
    });
});
