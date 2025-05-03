const FormHandler = {
    // Initialize the form handler with form ID, submission URL, and callback functions
    initialize: function(formId, submitUrl, successCallback, errorCallback) {
        const form = document.getElementById(formId);

        if (form) {
            console.log('Form found:', formId); // Debugging log
            
            // Search for the submit button within the form
            const submitButton = form.querySelector("button[type='submit']");
            
            if (submitButton) {
                console.log('Submit button found:', submitButton); // Debugging log
                
                // Add event listener for form submission
                form.addEventListener('submit', (event) => {
                    event.preventDefault(); // Prevent default form submission
                    
                    console.log('Form submission triggered'); // Debugging log

                    // Validate the form before submission
                    if (this.validate(form)) {
                        console.log('Form validation passed'); // Debugging log
                        
                        const formData = new FormData(form); // Create FormData object from the form
                        this.submitForm(submitUrl, formData, successCallback, errorCallback); // Submit the form via AJAX
                    } else {
                        console.error('Form validation failed.'); // Debugging log
                    }
                });
            } else {
                console.error('Submit button not found'); // Debugging log
            }
        } else {
            console.error(`Form with ID "${formId}" not found.`); // Debugging log
        }
    },

    // Validate form inputs
    validate: function(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                input.classList.add('error');
                const errorMessage = input.parentElement.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.textContent = `${input.name} is required.`;
                }
            } else {
                input.classList.remove('error');
                const errorMessage = input.parentElement.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.textContent = '';
                }
            }
        });

        console.log('Form validation result:', isValid); // Debugging log
        return isValid;
    },

    // Submit form via AJAX (Fetch API)
    submitForm: function(url, formData, successCallback, errorCallback) {
        console.log('Submitting form to:', url); // Debugging log
        console.log('Form data:', formData); // Debugging log
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Expect JSON response from backend
        .then(data => {
            console.log('Server response:', data); // Debugging log
            if (data.success) {
                successCallback(data); // Handle success callback
            } else {
                errorCallback(data); // Handle error callback
            }
        })
        .catch(error => {
            console.error('Error submitting the form:', error);
            if (errorCallback) {
                errorCallback({ error: 'There was an error submitting the form.' });
            }
        });
    }
};
