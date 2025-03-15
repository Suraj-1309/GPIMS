<nav class="navbar navbar-expand-lg navbar-light bg-light">

  <a class="navbar-brand" href="admin.php">
    <img src="logo.svg" alt="logo" class="logo d-inline-block align-top">
  </a>

  <!-- Navbar toggler for mobile view -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Navbar links -->
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link navbar-user" href="adminpanel/profile.php"
           style="font-weight: 700; font-size: 1.25rem; color: #0f0f0f;">
          USER: <?php echo $_SESSION['name']; ?>
        </a>
      </li>
    </ul>
  </div>
</nav>
