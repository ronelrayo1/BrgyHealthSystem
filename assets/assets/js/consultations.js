document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addModal');
    const modalForm = document.querySelector('.modal-form');

    if (addModal && modalForm) {
        function openAddModal() {
            addModal.classList.add('active');
        }

        function closeAddModal() {
            addModal.classList.remove('active');
        }

        window.openAddModal = openAddModal;
        window.closeAddModal = closeAddModal;

        window.deleteConsultation = function(consultationId, button) {
            if (!confirm('Are you sure you want to delete this consultation?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', consultationId);

            fetch('process/consultations_ajax.php', {
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
                    const row = button.closest('tr');
                    row.remove();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting consultation. Please try again.');
            });
        };

        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('process/consultations_ajax.php', {
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
                    closeAddModal();
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding consultation. Please try again.');
            });
        });

        addModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });
    }
});
