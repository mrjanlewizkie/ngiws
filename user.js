// Sidebar Toggle
const toggleBtn = document.getElementById("toggleBtn");
const sidebar = document.querySelector(".sidebar");

toggleBtn.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

// Tab Sections
const labSection = document.querySelector(".lab-section");
const viewRequestsSection = document.querySelector(".view-requests-section");
const allSections = [labSection, viewRequestsSection];

// Sidebar Nav Links
const navLinks = document.querySelectorAll(".sidebar ul li a");

navLinks.forEach(link => {
  link.addEventListener("click", () => {
    navLinks.forEach(l => l.classList.remove("active"));
    link.classList.add("active");

    const tabText = link.textContent.trim();
    allSections.forEach(section => (section.style.display = "none"));

    if (tabText === "Computer Labs") {
      labSection.style.display = "block";
    } else if (tabText === "View Requests") {
      viewRequestsSection.style.display = "block";
    } else if (tabText === "About") {
      alert("About page placeholder.");
    } else if (tabText === "Dark Mode") {
      alert("Dark Mode toggle placeholder.");
    } else if (tabText === "Log-Out") {
      alert("Logging out...");
    }
  });
});

// Close sidebar on outside click (mobile)
document.body.addEventListener("click", (e) => {
  if (sidebar.classList.contains("active")) {
    if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
      sidebar.classList.remove("active");
    }
  }
});

// Modal Logic
const labButtons = document.querySelectorAll(".lab-btn");
const modal = document.getElementById("requestModal");
const roomNameSpan = document.getElementById("roomName");
let selectedRoom = "";

labButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    selectedRoom = e.target.closest(".lab-card").getAttribute("data-room");
    roomNameSpan.textContent = selectedRoom;
    modal.classList.add("show");
  });
});

// Issue Checkboxes and Inputs
const softwareIssueCheckbox = document.getElementById("softwareIssue");
const otherIssueCheckbox = document.getElementById("otherIssue");
const softwareSpecifyInput = document.getElementById("softwareSpecify");
const otherSpecifyInput = document.getElementById("otherSpecify");

softwareIssueCheckbox.addEventListener("change", () => {
  softwareSpecifyInput.style.display = softwareIssueCheckbox.checked ? "block" : "none";
});

otherIssueCheckbox.addEventListener("change", () => {
  otherSpecifyInput.style.display = otherIssueCheckbox.checked ? "block" : "none";
});

// Submit Logic
const form = modal.querySelector("form");
const requestList = document.getElementById("requestList");

form.addEventListener("submit", (e) => {
  e.preventDefault();

  const issueCheckboxes = form.querySelectorAll('input[name="issues"]:checked');
  const priorityCheckboxes = form.querySelectorAll('input[name="priority"]:checked');

  // Validation
  if (
    issueCheckboxes.length === 0 &&
    !softwareIssueCheckbox.checked &&
    !otherIssueCheckbox.checked
  ) {
    alert("Please choose at least one issue.");
    return;
  }

  if (softwareIssueCheckbox.checked && !form.softwareSpecify.value.trim()) {
    alert("Please specify the software you want to install.");
    return;
  }

  if (otherIssueCheckbox.checked && !form.otherSpecify.value.trim()) {
    alert("Please specify the issue.");
    return;
  }

  if (priorityCheckboxes.length === 0) {
    alert("Choose your priority.");
    return;
  }

  if (priorityCheckboxes.length > 1) {
    alert("Choose only one priority.");
    return;
  }

  // Gather Data
  const requestorName = document.querySelector(".user-info h6").textContent;
  const selectedIssues = Array.from(issueCheckboxes).map(
    (cb) => cb.parentElement.textContent.trim()
  );

  if (softwareIssueCheckbox.checked) {
    selectedIssues.push("Software to install: " + form.softwareSpecify.value.trim());
  }

  if (otherIssueCheckbox.checked) {
    selectedIssues.push("Other: " + form.otherSpecify.value.trim());
  }

  const selectedPriority = priorityCheckboxes[0].parentElement.textContent.trim();

  // Get current date formatted (e.g., "Date: Oct 15, 2025")
  const options = { year: "numeric", month: "short", day: "numeric" };
  const currentDate = new Date().toLocaleDateString(undefined, options);
  const formattedDate = `Date: ${currentDate}`;

  // Create Request Card HTML
  const card = document.createElement("div");
  card.classList.add("card", "mb-3", "shadow-sm");

  card.innerHTML = `
    <div class="card-body d-flex justify-content-between flex-wrap">
      <div class="info-left">
        <p><i class="fas fa-user"></i> <strong>Requestor:</strong> ${requestorName}</p>
        <p><i class="fas fa-door-open"></i> <strong>Room:</strong> ${selectedRoom}</p>
        <p><i class="fas fa-exclamation-circle"></i> <strong>Report:</strong> ${selectedIssues.join(", ")}</p>
        <p><i class="fas fa-circle text-danger"></i> <strong>Priority:</strong> ${selectedPriority}</p>
      </div>
      <div class="info-right text-end">
        <p class="mb-2 small text-muted">${formattedDate}</p>
        <button class="btn btn-danger btn-sm delete-request" title="Delete request">
          <i class="fas fa-trash-alt"></i>
        </button>
      </div>
    </div>
  `;

  // Append new card on top of the request list
  requestList.prepend(card);

  // Delete button functionality
  card.querySelector(".delete-request").addEventListener("click", () => {
    card.remove();
  });

  // Switch to View Requests tab and update active link
  labSection.style.display = "none";
  viewRequestsSection.style.display = "block";

  navLinks.forEach((l) => l.classList.remove("active"));
  navLinks.forEach((l) => {
    if (l.textContent.trim() === "View Requests") {
      l.classList.add("active");
    }
  });

  // Close modal and reset form
  closeModal();
});

function closeModal() {
  modal.classList.remove("show");
  form.reset();
  softwareSpecifyInput.style.display = "none";
  otherSpecifyInput.style.display = "none";
}
