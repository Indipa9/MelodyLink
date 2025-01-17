document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('userType');
    const artistFields = document.getElementById('artistFields');
    const organizerFields = document.getElementById('organizerFields');
    const supplierFields = document.getElementById('supplierFields');

    // Initial check on page load
    updateAdditionalFields(userTypeSelect.value);

    userTypeSelect.addEventListener('change', function() {
        updateAdditionalFields(this.value);
    });

    function updateAdditionalFields(userType) {
        // Hide all additional fields first
        artistFields.style.display = 'none';
        organizerFields.style.display = 'none';
        supplierFields.style.display = 'none';

        // Show fields based on selected user type
        switch(userType) {
            case 'artist':
                artistFields.style.display = 'block';
                break;
            case 'organizer':
                organizerFields.style.display = 'block';
                break;
            case 'supplier':
                supplierFields.style.display = 'block';
                break;
        }
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const images = [
        '../img/a.jpg',
        // '../img/b.jpg',
        // '../img/c.jpg',
        // '../img/d.jpg',
        '../img/g.jpg',
        '../img/e.jpg',
        '../img/f.jpg',
        '../img/h.jpg',

    ];

    let index = 0;

    // Function to change background image
    function changeBackgroundImage() {
        document.body.style.backgroundImage = `url('${images[index]}')`;
        index = (index + 1) % images.length;
    }

    // Change background image every 5 seconds
    setInterval(changeBackgroundImage, 5000);

    // Initial background set
    changeBackgroundImage();
});