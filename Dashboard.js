document.addEventListener("DOMContentLoaded", function () {
    fetch('php/get_profile.php')
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          const user = data.user;
          document.getElementById("profile-name").value = user.username;
          document.getElementById("profile-email").value = user.email;
          document.getElementById("profile-phone").value = user.phone;
          document.getElementById("profile-medical-history").value = user.medical_history;
  
          // Profile completion check
          const profileStatus = document.getElementById("profile-status");
          if (user.phone && user.medical_history) {
            profileStatus.textContent = "✅ Profile Complete";
          } else {
            profileStatus.textContent = "❌ Profile Incomplete. Please update missing fields.";
          }
  
          // Load reminders
          loadReminders(user);
        } else {
          alert(data.message);
          window.location.href = "login.html";
        }
      });
  
    // Handle profile update
    document.getElementById("profile-form").addEventListener("submit", function (e) {
      e.preventDefault();
  
      const formData = new FormData();
      formData.append("username", document.getElementById("profile-name").value);
      formData.append("email", document.getElementById("profile-email").value);
      formData.append("phone", document.getElementById("profile-phone").value);
      formData.append("medical_history", document.getElementById("profile-medical-history").value);
  
      fetch('php/update_profile.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          location.reload();
        })
        .catch(err => console.error("Error:", err));
    });
  
    function loadReminders(user) {
      const list = document.getElementById("reminder-list");
      list.innerHTML = "";
  
      const today = new Date().toISOString().split("T")[0];
      const reminders = [
        `Take your iron supplements`,
        `Scheduled check-up on ${today}`,
        `Lab test reminder`
      ];
  
      reminders.forEach(reminder => {
        const li = document.createElement("li");
        li.textContent = reminder;
        list.appendChild(li);
      });
    }
  });
  