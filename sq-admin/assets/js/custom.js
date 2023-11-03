
const sidebar = document.querySelector('.sidebar');
const toggleButton = document.querySelector('.toggle-button');
const toggleButtonCustom = document.querySelector('.toggle-button-custom');
const closeSidebar = document.getElementById('closeSidebar'); // Add this line

closeSidebar.addEventListener('click', () => {
    sidebar.style.width = '0';
});

toggleButton.addEventListener('click', () => {
    if (sidebar.style.width === '0px' || !sidebar.style.width) {
        sidebar.style.width = '250px';
    } else {
        sidebar.style.width = '0';
    }
});

toggleButtonCustom.addEventListener('click', () => {
    if (sidebar.style.width === '0px' || !sidebar.style.width) {
        sidebar.style.width = '250px';
    } else {
        sidebar.style.width = '0';
    }
});



