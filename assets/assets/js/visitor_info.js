document.addEventListener('DOMContentLoaded', function() {
    const visitorForm = document.getElementById('visitorForm');

    if (visitorForm) {
        visitorForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'save_visitor_info');

            fetch('process/visitor_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'index.php?page=dashboard';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting information. Please try again.');
            });
        });
    }
});
