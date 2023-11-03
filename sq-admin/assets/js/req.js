function submitDatabaseDetails(event) {
    event.preventDefault();

    var form = document.querySelector('form');
    var formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    }).then(response => {
        // Handle the response if needed
    }).catch(error => {
        // Handle errors if any
    });
}
