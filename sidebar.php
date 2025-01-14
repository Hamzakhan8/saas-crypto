<div id="sidebar" class="sidebar glass-effect p-3">
    <h2 class="">Crypto Dashboard</h2>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="index.php"><i class="fa fa-home"></i> Dashboard</a>
        </li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin'): ?>
        <li class="nav-item">
            <a class="nav-link" href="roleeditor.php"><i class="fa fa-user"></i> Role Editor</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="trades/create_trade.php"><i class="fa fa-cogs"></i> Manage Trades</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
       
        <!-- Add more menu items as needed -->
    </ul>
</div>
