<?php 
include('../maranguide_connection.php');
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: /visitor/admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$owner_detail = "SELECT * FROM adminlist WHERE admin_id = ?";
$stmt = $conn->prepare($owner_detail);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin) {
    $_SESSION['admin_name'] = $admin['admin_name'];
}


?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="../css/project.css">
    <link rel="stylesheet" href="src/css/superadmin_sidenav.css">
</head>
<body>
<nav class="nav-wrapper">
    <a href="superadmin_dashboard.php" class="brand-logo">
        <img src="/media/icons/MARANGUIDE_ICON.png" alt="MaranGuide Logo">
    </a>
    <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>

    <!-- Desktop Nav -->
    <ul class="right hide-on-med-and-down">
        <li><a href="superadmin_dashboard.php"><i class="material-icons">dashboard</i></a></li>
        <li><a href="superadmin_manage_attraction.php"><i class="material-icons">store</i></a></li>
        <li><a href="superadmin_manage_owner.php"><i class="material-icons">event</i></a></li>
        <li><a href="superadmin_view_feedback.php"><i class="material-icons">feedback</i></a></li>
        <li>
            <a class="dropdown-trigger" href="#" data-target="dropdown_header_desktop">
                <i class="material-icons">account_circle</i>
            </a>
        </li>
    </ul>
</nav>

<!-- Desktop Dropdown Content -->
<ul id="dropdown_header_desktop" class="dropdown-content">
    <li><a href="owner_profile.php"><i class="material-icons">person</i>Profile</a></li>
    <li><a href="change_password.php"><i class="material-icons">lock</i>Change Password</a></li>
    <li class="divider"></li>
    <li><a href="#" onclick="confirmLogout()"><i class="material-icons">exit_to_app</i>Log Out</a></li>
</ul>

<!-- Mobile Menu (Sidenav) -->
<ul class="sidenav" id="mobile-demo">
    <li>
        <div class="user-view">
            <div class="background orange">
            </div>
            <span class="white-text name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Owner'); ?></span>
        </div>
    </li>
    <li><a href="superadmin_dashboard.php"><i class="material-icons">dashboard</i>Dashboard</a></li>
    <li><a href="superadmin_manage_attraction.php"><i class="material-icons">store</i>My Attraction</a></li>
    <li><a href="superadmin_manage_owner.php"><i class="material-icons">event</i>Events</a></li>
    <li><a href="superadmin_manage_feedback.php"><i class="material-icons">feedback</i>Feedback</a></li>
    <li class="divider"></li>
    <li><a href="superadmin_profile.php"><i class="material-icons">person</i>Profile</a></li>
    <li><a href="change_password.php"><i class="material-icons">lock</i>Change Password</a></li>
    <li><a href="#" onclick="confirmLogout()"><i class="material-icons">exit_to_app</i>Log Out</a></li>
</ul>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all Materialize components
    var sidenav = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenav);
    
    var dropdowns = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(dropdowns, {
        constrainWidth: false,
        coverTrigger: false
    });
});

function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}
</script>
</body>
</html>