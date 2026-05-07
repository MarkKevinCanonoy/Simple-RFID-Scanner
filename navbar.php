<?php

$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="nav-brand">
        <span class="brand-icon">&#9670;</span>
        <span class="brand-text">RFID<span class="brand-accent">SYS</span></span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="index.php" class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>">
                <span class="nav-icon">&#43;</span> Register
            </a>
        </li>
        <li>
            <a href="dashboard.php" class="nav-link <?= $current === 'dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon">&#9776;</span> Dashboard
            </a>
        </li>
        <li>
            <a href="scan.php" class="nav-link <?= $current === 'scan.php' ? 'active' : '' ?>">
                <span class="nav-icon">&#9654;</span> Scan RFID
            </a>
        </li>
    </ul>
</nav>