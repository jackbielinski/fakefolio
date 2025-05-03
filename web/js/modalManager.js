const modalManager = {
    load: function(url, callback) {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('modal-content').innerHTML = data;
                if (callback && typeof callback === 'function') {
                    callback();
                }
                // Remove hidden class to show modal
                document.getElementById('modal-content').classList.remove('hidden');
                // Show the modal
                document.getElementById('modal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error loading modal content:', error);
            });
    },

    close: function () {
        document.getElementById('modal').style.display = 'none';
    },

    closeOnClick: function (event) {
        if (event.target === document.getElementById('modal')) {
            this.close();
        }
    }
};

// Add event listener for closing the modal when clicking outside the content
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modal');
    if (modalElement) {
        modalElement.addEventListener('click', function(event) {
            modalManager.closeOnClick(event);
        });
    } else {
        console.error('Modal element with ID "modal" not found.');
    }
});