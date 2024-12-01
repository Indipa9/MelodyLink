document.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll(".tab-button");
    const tabContents = document.querySelectorAll(".tab-content");
  
    tabButtons.forEach((button) => {
      button.addEventListener("click", () => {
        // Remove active class from all buttons
        tabButtons.forEach((btn) => btn.classList.remove("active"));
        // Hide all tab contents
        tabContents.forEach((content) => content.classList.remove("active"));
  
        // Add active class to clicked button
        button.classList.add("active");
        // Show corresponding tab content
        const tabId = button.getAttribute("data-tab");
        document.getElementById(tabId).classList.add("active");
      });
    });
  
    // Add event listeners for "Leave" buttons
    const leaveButtons = document.querySelectorAll(".leave-btn");
    leaveButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        alert("You have left this community.");
        btn.closest(".community").remove();
      });
    });
  
    // Add event listeners for "Withdraw" buttons
    const withdrawButtons = document.querySelectorAll(".withdraw-btn");
    withdrawButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        alert("You have withdrawn your request.");
        btn.closest(".community").remove();
      });
    });
  });
  