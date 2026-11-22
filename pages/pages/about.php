<?php
require_once __DIR__ . '/../config/page_init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include __DIR__ . '/../components/header.php'; ?>
</head>
<body>
    <div class="about-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>About Our Health Center</h1>
            <p>Dedicated to providing quality healthcare services to our community</p>
        </div>
    </div>

    <div class="about-section">
        <div class="about-container">
            <div class="mission-vision-section">
                <div class="mv-grid">
                    <div class="mv-card mission">
                        <div class="mv-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <h3>Our Mission</h3>
                        <p>To provide accessible, affordable, and quality healthcare services to every resident of Barangay Handumanan, ensuring the well-being of our community through compassionate care and modern medical practices.</p>
                    </div>

                    <div class="mv-card vision">
                        <div class="mv-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </div>
                        <h3>Our Vision</h3>
                        <p>To be the leading barangay health center in the region, recognized for excellence in patient care, innovative health programs, and commitment to community health education and disease prevention.</p>
                    </div>
                </div>
            </div>

            <div class="about-story">
                <div class="story-content">
                    <h2>Our Story</h2>
                    <p class="story-intro">The Barangay Handumanan Health Center has been serving our community for over two decades, evolving from a simple clinic to a modern healthcare facility equipped with advanced medical technology and dedicated healthcare professionals.</p>
                    <p>Our journey began with a small team of committed health workers who saw the need for organized, accessible healthcare in our barangay. Today, we stand as a beacon of hope and healing, serving thousands of patients annually with dignity and compassion.</p>
                    <p>We believe that quality healthcare is a right, not a privilege. Every day, we strive to make healthcare more accessible, efficient, and patient-centered through our digital records system and innovative health programs.</p>
                </div>
            </div>

            <div class="stats-section">
                <div class="stats-container">
                    <h2>Our Impact</h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number" data-target="5000">0</div>
                            <div class="stat-label">Patients Served</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="25">0</div>
                            <div class="stat-label">Healthcare Staff</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="15">0</div>
                            <div class="stat-label">Years of Service</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="98">0</div>
                            <div class="stat-label">Satisfaction Rate %</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="features-section">
                <div class="section-header">
                    <h2>Why Choose Us</h2>
                    <p>We combine modern technology with compassionate care to serve you better</p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3>Secure Records</h3>
                        <p>Your medical information is protected with advanced security measures and confidential handling.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h3>Quick Appointments</h3>
                        <p>Easy online booking system to schedule your consultations at your convenience.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3>Expert Team</h3>
                        <p>Qualified doctors, nurses, and healthcare professionals dedicated to your well-being.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                        </div>
                        <h3>Modern Facilities</h3>
                        <p>State-of-the-art medical equipment and comfortable facilities for better patient experience.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <h3>Compassionate Care</h3>
                        <p>We treat every patient with dignity, respect, and genuine concern for their health.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </div>
                        <h3>Health Education</h3>
                        <p>Regular health programs and workshops to educate our community on disease prevention.</p>
                    </div>
                </div>
            </div>

            <div class="team-section">
                <div class="section-header">
                    <h2>Our Leadership</h2>
                    <p>Meet the dedicated team behind our health center</p>
                </div>

                <div class="team-grid">
                    <div class="team-card">
                        <div class="team-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3>Dr. Joshua Garcia</h3>
                        <p class="team-role">Chief Medical Officer</p>
                        <p class="team-bio">20+ years of experience in community healthcare and preventive medicine.</p>
                    </div>

                    <div class="team-card">
                        <div class="team-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3>Nurse Max Collins</h3>
                        <p class="team-role">Head Nurse</p>
                        <p class="team-bio">Specialized in maternal and child health with 15 years of nursing experience.</p>
                    </div>

                    <div class="team-card">
                        <div class="team-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3>Mr. Ronel Rayo</h3>
                        <p class="team-role">Barangay Health Worker</p>
                        <p class="team-bio">Community health advocate dedicated to outreach programs and health education.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="about-cta">
        <div class="cta-content">
            <h2>Join Our Healthcare Family</h2>
            <p>Register today to access our services and manage your health records online</p>
            <a href="index.php?page=register" class="cta-btn">Register Now</a>
        </div>
    </div>
</body>
</html>
