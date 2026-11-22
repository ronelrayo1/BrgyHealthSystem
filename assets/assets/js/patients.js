document.addEventListener('DOMContentLoaded', function() {
    const patientModal = document.getElementById('patientModal');
    const patientForm = document.getElementById('patientForm');
    const profilePictureInput = document.getElementById('profilePictureInput');
    const searchInput = document.getElementById('searchInput');
    const qrCodeModal = document.getElementById('qrCodeModal');
    const patientProfileModal = document.getElementById('patientProfileModal');

    if (patientModal && patientForm) {
        function openModal() {
            patientModal.classList.add('active');
            document.getElementById('modalTitle').textContent = 'Add Patient';
            patientForm.reset();
            document.getElementById('patientId').value = '';
            resetProfilePreview();
        }

        function resetProfilePreview() {
            const preview = document.getElementById('profilePreview');
            const removeBtn = document.getElementById('removeProfileBtn');
            preview.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="4"></circle>
                </svg>
                <span>Click to upload profile picture</span>
            `;
            preview.style.backgroundImage = 'none';
            removeBtn.style.display = 'none';
        }

        function removeProfilePicture() {
            const id = document.getElementById('patientId').value;
            if (id) {
                const form = new FormData();
                form.append('action', 'remove_profile');
                form.append('id', id);

                fetch('process/patients_ajax.php', {
                    method: 'POST',
                    body: form
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('profilePictureInput').value = '';
                        resetProfilePreview();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing profile picture');
                });
            } else {
                document.getElementById('profilePictureInput').value = '';
                resetProfilePreview();
            }
        }

        if (profilePictureInput) {
            profilePictureInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('profilePreview');
                const removeBtn = document.getElementById('removeProfileBtn');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.style.backgroundImage = `url(${e.target.result})`;
                        preview.style.backgroundSize = 'cover';
                        preview.style.backgroundPosition = 'center';
                        preview.innerHTML = '';
                        removeBtn.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function closeModal() {
            patientModal.classList.remove('active');
        }

        window.openModal = openModal;
        window.closeModal = closeModal;
        window.removeProfilePicture = removeProfilePicture;
    }

    if (qrCodeModal) {
        function patients_viewQRCode(patientId, qrFilename) {
            const qrUrl = 'uploads/qr_codes/' + qrFilename;
            document.getElementById('qrCodeImage').src = qrUrl;
            qrCodeModal.classList.add('active');
        }

        function patients_closeQRModal() {
            qrCodeModal.classList.remove('active');
        }

        function patients_downloadQRCode() {
            const qrImage = document.getElementById('qrCodeImage');
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = 'patient_qr_code.png';
            link.click();
        }

        window.viewQRCode = patients_viewQRCode;
        window.closeQRModal = patients_closeQRModal;
        window.downloadQRCode = patients_downloadQRCode;
    }

    if (patientProfileModal) {
        function patients_openPatientProfile(patientId) {
            console.log('Opening patient profile for ID:', patientId);

            fetch('process/patients_ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=get_patient&id=' + patientId
            })
            .then(response => {
                console.log('Response received');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (data.success) {
                    const patient = data.patient;
                    const content = document.getElementById('patientProfileContent');

                    content.innerHTML = `
                        <div class="profile-modal-header">
                            ${patient.profile_picture ?
                                `<img src="uploads/profile_pictures/${patient.profile_picture}" alt="Profile" class="profile-modal-picture">` :
                                `<div class="profile-modal-initials">${patient.first_name.charAt(0).toUpperCase()}</div>`
                            }
                            <div class="profile-modal-info">
                                <h3>${patient.first_name} ${patient.last_name}</h3>
                                <div class="profile-modal-badges">
                                    <span class="patient-gender">${patient.gender}</span>
                                    ${patient.age ? `<span class="patient-age">${patient.age} years old</span>` : ''}
                                </div>
                            </div>
                        </div>

                        <div class="profile-modal-details">
                            <div class="profile-detail-section">
                                <h4>Personal Information</h4>
                                <div class="detail-row">
                                    <span class="detail-label">Patient ID:</span>
                                    <span class="detail-value">#${patient.id}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">First Name:</span>
                                    <span class="detail-value">${patient.first_name}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Last Name:</span>
                                    <span class="detail-value">${patient.last_name}</span>
                                </div>
                                ${patient.middle_name ? `
                                <div class="detail-row">
                                    <span class="detail-label">Middle Name:</span>
                                    <span class="detail-value">${patient.middle_name}</span>
                                </div>
                                ` : ''}
                                <div class="detail-row">
                                    <span class="detail-label">Birth Date:</span>
                                    <span class="detail-value">${patient.birth_date}</span>
                                </div>
                                ${patient.age ? `
                                <div class="detail-row">
                                    <span class="detail-label">Age:</span>
                                    <span class="detail-value">${patient.age} years old</span>
                                </div>
                                ` : ''}
                                <div class="detail-row">
                                    <span class="detail-label">Gender:</span>
                                    <span class="detail-value">${patient.gender}</span>
                                </div>
                            </div>

                            <div class="profile-detail-section">
                                <h4>Contact Information</h4>
                                <div class="detail-row">
                                    <span class="detail-label">Contact Number:</span>
                                    <span class="detail-value">${patient.contact_number}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Address:</span>
                                    <span class="detail-value">${patient.address}</span>
                                </div>
                            </div>

                            ${patient.guardian_name ? `
                            <div class="profile-detail-section">
                                <h4>Guardian Information</h4>
                                <div class="detail-row">
                                    <span class="detail-label">Guardian Name:</span>
                                    <span class="detail-value">${patient.guardian_name}</span>
                                </div>
                            </div>
                            ` : ''}

                            ${patient.qr_code ? `
                            <div class="profile-detail-section qr-section">
                                <h4>QR Code</h4>
                                <div class="qr-display">
                                    <img src="uploads/qr_codes/${patient.qr_code}" alt="QR Code">
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `;

                    console.log('Modal element:', patientProfileModal);
                    patientProfileModal.classList.add('active');
                    console.log('Modal opened');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading patient profile. Please try again.');
            });
        }

        function patients_closePatientProfileModal() {
            patientProfileModal.classList.remove('active');
        }

        function patients_openProfile(patientId, userId) {
            console.log('openProfile called with:', patientId, userId);
            patients_openPatientProfile(patientId);
        }

        window.openPatientProfile = patients_openPatientProfile;
        window.closePatientProfileModal = patients_closePatientProfileModal;
        window.openProfile = patients_openProfile;
    }

    function patients_generateQRCode(patientId) {
        if (confirm('Generate QR code for this patient?')) {
            const form = new FormData();
            form.append('action', 'generate_qr');
            form.append('id', patientId);

            fetch('process/patients_ajax.php', {
                method: 'POST',
                body: form
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating QR code. Please try again.');
            });
        }
    }

    function patients_editPatient(id) {
        fetch('process/patients_ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=get_patient&id=' + id
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('patientModal').classList.add('active');
                document.getElementById('modalTitle').textContent = 'Edit Patient';
                document.getElementById('patientId').value = data.patient.id;
                document.getElementById('patientForm').first_name.value = data.patient.first_name;
                document.getElementById('patientForm').last_name.value = data.patient.last_name;
                document.getElementById('patientForm').middle_name.value = data.patient.middle_name || '';
                document.getElementById('patientForm').birth_date.value = data.patient.birth_date;
                document.getElementById('patientForm').gender.value = data.patient.gender;
                document.getElementById('patientForm').contact_number.value = data.patient.contact_number;
                document.getElementById('patientForm').address.value = data.patient.address;
                document.getElementById('patientForm').guardian_name.value = data.patient.guardian_name || '';

                const preview = document.getElementById('profilePreview');
                const removeBtn = document.getElementById('removeProfileBtn');
                if (data.patient.profile_picture) {
                    const imageUrl = 'uploads/profile_pictures/' + data.patient.profile_picture;
                    preview.style.backgroundImage = `url(${imageUrl})`;
                    preview.style.backgroundSize = 'cover';
                    preview.style.backgroundPosition = 'center';
                    preview.innerHTML = '';
                    removeBtn.style.display = 'inline-block';
                } else {
                    resetProfilePreview();
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading patient data. Please try again.');
        });
    }

    function patients_deletePatient(id) {
        if (confirm('Are you sure you want to delete this patient?')) {
            const form = new FormData();
            form.append('action', 'delete');
            form.append('id', id);

            fetch('process/patients_ajax.php', {
                method: 'POST',
                body: form
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting patient. Please try again.');
            });
        }
    }

    window.generateQRCode = patients_generateQRCode;
    window.editPatient = patients_editPatient;
    window.deletePatient = patients_deletePatient;

    if (patientForm) {
        patientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = new FormData(this);
            const id = document.getElementById('patientId').value;
            form.append('action', id ? 'edit' : 'add');
            if (id) form.append('id', id);

            fetch('process/patients_ajax.php', {
                method: 'POST',
                body: form
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving patient. Please try again.');
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                window.location.href = 'index.php?page=patients&search=' + encodeURIComponent(this.value);
            }
        });
    }
});
