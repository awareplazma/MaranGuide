<?php
include('../maranguide_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (isset($_POST['admin_id']) && isset($_POST['admin_password'])) 
    {
        $admin_id = $_POST['admin_id'];
        $admin_password = $_POST['admin_password'];

        // SQL to retrieve user data
        $sql = "SELECT * FROM adminlist WHERE admin_id = ? AND admin_password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $admin_id, $admin_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) 
        {
            $admin = $result->fetch_assoc();

            // Store the admin ID and role in the session
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['role'] = $admin['admin_role']; 

            // Check if the user is a superadmin or admin
            if ($admin['admin_role'] === 'superadmin') {
                header("Location: ../superadmin/superadmin_dashboard.php");
                exit();
            } 
            
            if ($admin['admin_role'] === 'owner') {
                header("Location: ../admin/admin_dashboard.php");
                exit();  
            }
        } else {
            $_SESSION['error_message'] = "Invalid credentials";
        }
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>MaranGuide</title>
  <!-- Materialize CSS CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <!-- Google Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!-- Materialize JS CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <link rel="stylesheet" href="../project.css">
</head>
<body>
  <!-- Header -->
  <div id="header-html"></div>
   <div class="container">
    <!-- Message -->
    <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error-message">
                    <i class='bx bxs-error'></i>
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message success-message">
                    <i class='bx bxs-check-square'></i>
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
    <div class="admin-login-container">
        <h4 class="login-title">LOG MASUK ADMIN</h4>
        <img src="tttt" alt="MaranGuide Logo" class="brand-logo">
        <form method="POST" action="">
            <div class="input-field">
              <input type="text" id="username" name="admin_id" placeholder="NOMBOR ID" class="grey lighten-3" required>
            </div>
            
            <div class="input-field">
                <input type="password" id="password" name="admin_password" placeholder="KATA LALUAN" class="grey lighten-3" required>
            </div>
            <button type="submit" class="waves-effect waves-light btn-large">Log Masuk</button>

            <div class="forgot-password">
                <a href="#">LUPA KATA LALUAN?</a>
            </div>
        </form>
    </div>
</div>

  <script>
    //Fetch header
  document.addEventListener('DOMContentLoaded', function() {
    // Fetch header and initialize dropdown after it's loaded
    fetch('/visitor/header.html')
      .then(response => response.text())
      .then(data => {
        document.getElementById('header-html').innerHTML = data;

        // Sidenav
        var elems = document.querySelectorAll('.sidenav');
        var instances = M.Sidenav.init(elems);

        // Initialize dropdowns after the header is loaded
        var dropdownElems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdownElems, {
          coverTrigger: false, 
          constrainWidth: false, 
          alignment: 'right' 
        });
      })
      .catch(error => console.error('Error loading the header:', error));
  });
  </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="/js/ui.js"></script>
</body>
</html>
