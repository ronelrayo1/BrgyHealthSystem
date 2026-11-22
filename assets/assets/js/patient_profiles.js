document.addEventListener('DOMContentLoaded', function() {
    const patientProfileModal = document.getElementById('patientProfileModal');
    const qrCodeModal = document.getElementById('qrCodeModal');
    const searchPatients = document.getElementById('searchPatients');

    if (patientProfileModal) {
        function profiles_openPatientProfile(patientId) {
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
                    console.log('Modal opened successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading patient profile. Please try again.');
            });
        }

        function profiles_closePatientProfileModal() {
            patientProfileModal.classList.remove('active');
        }

        function profiles_openProfile(patientId, userId) {
            console.log('openProfile called with:', patientId, userId);
            profiles_openPatientProfile(patientId);
        }

        window.openPatientProfile = profiles_openPatientProfile;
        window.closePatientProfileModal = profiles_closePatientProfileModal;
        window.openProfile = profiles_openProfile;
    }

    if (qrCodeModal) {
        function profiles_viewQRCode(patientId, qrFilename) {
            const qrUrl = 'uploads/qr_codes/' + qrFilename;
            document.getElementById('qrCodeImage').src = qrUrl;
            qrCodeModal.classList.add('active');
        }

        function profiles_closeQRModal() {
            qrCodeModal.classList.remove('active');
        }

        function profiles_downloadQRCode() {
            const qrImage = document.getElementById('qrCodeImage');
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = 'patient_qr_code.png';
            link.click();
        }

        window.viewQRCode = profiles_viewQRCode;
        window.closeQRModal = profiles_closeQRModal;
        window.downloadQRCode = profiles_downloadQRCode;
    }

    function profiles_generateQRCode(patientId) {
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

    window.generateQRCode = profiles_generateQRCode;

    if (searchPatients) {
        function filterPatients() {
            const input = document.getElementById('searchPatients');
            const filter = input.value.toLowerCase();
            const cards = document.querySelectorAll('.patient-card');

            cards.forEach(card => {
                const name = card.dataset.name;
                if (name.includes(filter)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        window.filterPatients = filterPatients;
    }
});
