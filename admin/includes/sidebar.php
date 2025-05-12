<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Sidebar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
  
    .sidebar{
        background-color:black;
    }
    .sidebar .nav-link {
      color: white;
      padding: 10px 10px;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #495057;
    }
    .sidebar-toggle-btn {
      background: none;
      border: none;
      color: white;
    }
  </style>
</head>
<body>
<div class="d-flex flex-column p-3 text-white h-100" style="background-color: black; min-height: 100vh;">

  <button class="btn sidebar-toggle-btn d-md-none mb-3" onclick="toggleSidebar()">
    <i class="fas fa-times"></i>
  </button>

 <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link" href="/khairulquran/admin/dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/khairulquran/admin/courses/manage_courses.php">
                <i class="bi bi-book me-2"></i> Courses
            </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/khairulquran/admin/students/manage_students.php">

                <i class="bi bi-people me-2"></i> Students
            </a>
        </li>
        <li class="nav-item">
 <a class="nav-link" href="/khairulquran/admin/teachers/manage_teachers.php">

                <i class="bi bi-person-badge me-2"></i> Teachers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/khairulquran/admin/enrollments/manage_enrollments.php">
                <i class="bi bi-clipboard-check me-2"></i> Enrollments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/khairulquran/admin/payments/manage_payments.php">
                <i class="bi bi-file-earmark-text me-2"></i>Manage Payments
            </a>
        </li>
      
    
      </ul>

  <div class="mt-auto">
    <form action="../includes/logout.php" method="POST">
      <button type="submit" class="btn btn-danger w-100 mt-3">
        <i class="fas fa-power-off me-2"></i> Logout
      </button>
    </form>
  </div>
</div>


<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('d-none');
  }
</script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
  
</body>
</html>
