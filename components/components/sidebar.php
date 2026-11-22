<?php
require_once __DIR__ . '/../config/sidebar_config.php';
?>
<aside>
    <div class="sidebar-menu">

        <?php if (!in_array($userRole, ['admin', 'staff', 'doctor', 'visitor'])): ?>
        <a href="<?php echo $basePath; ?>index.php?page=dashboard" class="sidebar-link <?php echo $pages->getActiveClass('dashboard'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'admin'): ?>
        <a href="<?php echo $basePath; ?>index.php?page=dashboard" class="sidebar-link <?php echo $pages->getActiveClass('dashboard'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=medicine_inventory" class="sidebar-link <?php echo $pages->getActiveClass('medicine_inventory'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span>Medicine Inventory</span>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=users" class="sidebar-link <?php echo $pages->getActiveClass('users'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>User Management</span>
            <?php if ($pendingCount > 0): ?>
            <span class="notification-badge">
                <i class="fas fa-bell"></i>
                <span class="badge-count"><?php echo $pendingCount; ?></span>
            </span>
            <?php endif; ?>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=patient_profiles" class="sidebar-link <?php echo $pages->getActiveClass('patient_profiles'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            </svg>
            <span>Patient Profiles (QR)</span>
        </a>
        <?php endif; ?>



        <?php if (in_array($userRole, ['staff', 'doctor'])): ?>
        <a href="<?php echo $basePath; ?>index.php?page=patients" class="sidebar-link <?php echo $pages->getActiveClass('patients'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Patients</span>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=medical_records" class="sidebar-link <?php echo $pages->getActiveClass('medical_records'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>Medical Records</span>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=consultations" class="sidebar-link <?php echo $pages->getActiveClass('consultations'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            <span>Consultations</span>
        </a>
        <?php endif; ?>

        <?php if (in_array($userRole, ['patient', 'guardian'])): ?>
        <a href="<?php echo $basePath; ?>index.php?page=my_records" class="sidebar-link <?php echo $pages->getActiveClass('my_records'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>My Records</span>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=appointments" class="sidebar-link <?php echo $pages->getActiveClass('appointments'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>Appointments</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'visitor'): ?>
        <?php if (!empty($_SESSION['visitor_info_completed'])): ?>
        <a href="<?php echo $basePath; ?>index.php?page=dashboard" class="sidebar-link <?php echo $pages->getActiveClass('dashboard'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo $basePath; ?>index.php?page=visit_patient" class="sidebar-link <?php echo $pages->getActiveClass('visit_patient'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Visit Patient</span>
        </a>


        <?php else: ?>
        <a href="<?php echo $basePath; ?>index.php?page=visitor_info" class="sidebar-link <?php echo $pages->getActiveClass('visitor_info'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Complete Info</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</aside>