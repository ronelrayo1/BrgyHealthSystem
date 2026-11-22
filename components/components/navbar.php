<?php
require_once __DIR__ . '/../config/navbar_config.php';
?>
<nav>
  <div class="nav-brand">
    <img src="<?php echo $basePath; ?>assets/logo/icon.png" alt="logo" width="80px">
    <h1 id="h1">Baranggay Handumanan Health Center Records System</h1>
  </div>
  <div class="nav-links">
    <input type="search" placeholder="Search..." id="search" onkeyup="filterLinks()">
    <ul>
      <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'staff', 'doctor'])): ?>
      <li><a href="<?php echo $basePath; ?>index.php?page=home">Home</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=services">Services</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=about">About</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=contact">Contact</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=logout">Logout</a></li>
      <?php elseif (!in_array($_SESSION['role'] ?? '', ['admin', 'staff', 'doctor', 'visitor'])): ?>
      <li><a href="<?php echo $basePath; ?>index.php?page=home">Home</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=services">Services</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=about">About</a></li>
      <li><a href="<?php echo $basePath; ?>index.php?page=contact">Contact</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>