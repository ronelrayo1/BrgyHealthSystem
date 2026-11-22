<?php
require_once __DIR__ . '/config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'components/header.php'; ?>
</head>
<body>

  <?php if (in_array($_GET['page'] ?? 'home', ['home', 'services', 'about', 'contact', 'login', 'register', 'forgot_password'])): ?>
  <?php include 'components/bg_slider.php'; ?>
  <?php endif; ?>

  <?php if (in_array($_GET['page'] ?? 'home', ['dashboard', 'visit_patient']) && ($_SESSION['role'] ?? '') === 'visitor'): ?>
  <nav>
    <div class="nav-brand">
      <img src="assets/logo/icon.png" alt="logo" width="80px">
      <h1 id="h1">Baranggay Handumanan Health Center Records System</h1>
    </div>
    <div class="nav-links">
      <input type="search" placeholder="Search..." id="search" onkeyup="filterLinks()">
      <ul>
        <li><a href="index.php?page=logout">Logout</a></li>
      </ul>
    </div>
  </nav>
  <?php elseif (!in_array($_GET['page'] ?? 'home', ['login', 'register', 'forgot_password', 'visitor_info'])): ?>
  <?php include 'components/navbar.php'; ?>
  <?php endif; ?>

  <?php include 'components/alerts.php'; ?>

  <?php Pages::display($_GET['page'] ?? 'home'); ?>

  <?php include 'components/login_form.php'; ?>

</body>
</html>