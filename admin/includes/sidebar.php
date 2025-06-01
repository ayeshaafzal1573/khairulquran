<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Sidebar</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Font Awesome for power icon and toggle icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    .sidebar {
      background-color: black;
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

<div id="sidebar" class="d-flex flex-column p-3 text-white sidebar" style="min-height: 100vh;">

  <!-- Toggle button (for small screens) -->
  <button class="btn sidebar-toggle-btn d-md-none mb-3" onclick="toggleSidebar()">
    <i class="fas fa-times"></i>
  </button>

  <!-- Sidebar Navigation -->
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link" href="/admin/dashboard.php">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/courses/manage_courses.php">
        <i class="bi bi-book me-2"></i> Courses
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/students/manage_students.php">
        <i class="bi bi-people me-2"></i> Students
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/teachers/manage_teachers.php">
        <i class="bi bi-person-badge me-2"></i> Teachers
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/enrollments/manage_enrollments.php">
        <i class="bi bi-clipboard-check me-2"></i> Enrollments
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/payments/manage_payments.php">
        <i class="bi bi-file-earmark-text me-2"></i> Manage Payments
      </a>
    </li>
  </ul>

 <!-- Logout Button -->
<div class="mt-auto">
  <form action="/admin/logout.php" method="POST">
    <button type="submit" class="btn btn-danger w-100 mt-3">
      <i class="fas fa-power-off me-2"></i> Logout
    </button>
  </form>
</div>


</div>

<!-- Scripts -->
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('d-none');
  }
</script>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    window.onload = function () {
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

</body>
</html>