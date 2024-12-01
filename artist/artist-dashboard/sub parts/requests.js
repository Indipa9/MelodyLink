function switchTab(tabId) {
    // Get all sections and tabs
    const sections = document.querySelectorAll('.section');
    const tabs = document.querySelectorAll('.tab');
  
    // Hide all sections and remove active class from tabs
    sections.forEach(section => section.classList.add('hidden'));
    tabs.forEach(tab => tab.classList.remove('active'));
  
    // Show the selected section and activate the clicked tab
    document.getElementById(`${tabId}-section`).classList.remove('hidden');
    document.querySelector(`button[onclick="switchTab('${tabId}')"]`).classList.add('active');
  }
  