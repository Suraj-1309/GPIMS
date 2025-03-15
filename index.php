<?php
session_start();
if (isset($_SESSION['popup_message'])) {
  $alertClass = ($_SESSION['popup_type'] === 'success') ? 'alert-success' : 'alert-danger';
  echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert" id="popupMessage">'
    . $_SESSION['popup_message'] .
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
         </div>';
  unset($_SESSION['popup_message']);
  unset($_SESSION['popup_type']);
}
?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
      var popup = document.getElementById('popupMessage');
      if (popup) {
        popup.style.transition = "opacity 1s ease";
        popup.style.opacity = 0;
        setTimeout(function () {
          if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
          }
        }, 1000);
      }
    }, 3000); // 3 seconds delay before fade-out
  });
</script>

<?php include 'login.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login Page</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <!-- AOS Library for animations -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
  <style>
    /* Keep existing Navbar styles (do not modify content inside nav) */
    nav {
      border-bottom: 5px solid red;
      box-shadow: 0px 5px 5px -5px rgba(0, 0, 0, 1);
      box-sizing: border-box;
    }

    #loginButton:hover {
      border: 5px solid rgb(40, 143, 238);
    }

    /* Global Styles */
    body {
      font-family: Arial, sans-serif;
      padding-top: 70px;
      /* accommodate fixed navbar */
      margin: 0;
    }

    /* Hero Section */
    .hero-section {
      background: url('hero-image.jpg') no-repeat center center/cover;
      color: #fff;
      padding: 100px 20px;
      position: relative;
      text-align: center;
      background: url('images/image-copy.png') no-repeat center center/cover;
      font-weight: 900;
    }


    .hero-section::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }

    .hero-section .container {
      position: relative;
      z-index: 1;
    }

    /* About Section */
    .about-section {
      padding: 60px 20px;
      background-color: #f8f9fa;
      text-align: center;
    }

    /* Features Section */
    .features-section {
      padding: 60px 20px;
      background-color: #fff;
    }

    .feature-box {
      text-align: center;
      padding: 20px;
      margin-bottom: 30px;
    }

    .feature-box i {
      margin-bottom: 15px;
    }

    /* Contact Developer Section */
    .contact-section {
      padding: 60px 20px;
      background-color: #f8f9fa;
      text-align: center;
    }

    /* Footer Section */
    .footer-section {
      background-color: #333;
      color: #fff;
      padding: 20px;
      text-align: center;
    }

    .navbar,
    .container-fluid {
      flex-wrap: nowrap;
      white-space: nowrap;
    }

    /* Responsive adjustments */
    /* Responsive adjustments */
    @media (max-width: 767px) {

      nav {
        width: 100vw;
      }

      nav #logoImg{
        padding: 0%;
        margin: 0%;
        margin-left: -25;
      }

      nav #loginBtn{
        position: absolute;
        right: 5%;
      }

      #vanish {
        display: none;
      }

      .hero-section {
        padding: 80px 10px;
      }

      .about-section,
      .features-section,
      .contact-section {
        padding: 40px 10px;
      }

      /* Shrink feature icons and text on mobile */
      .feature-box i {
        font-size: 2rem;
      }

      .feature-box h4 {
        font-size: 1.1rem;
      }

      .feature-box p {
        font-size: 0.9rem;
      }

      /* Adjust navbar brand size if needed */
      .navbar-brand h5 {
        font-size: 1rem;
      }
    }
/* For mobile devices */
#loginModal .modal-dialog {
  max-width: 90%;
  margin: auto;
  display: flex;
  align-items: center;
  min-height: calc(100vh - 3rem); /* A bit less than 100vh to account for browser chrome */
}

 /* For mobile devices: Ensure modal width does not exceed 100% */
 @media (max-width: 767px) {

    #loginModal .modal-dialog {
      width: 70%;
      margin-left: 5%;
      box-sizing: border-box;
    }

    #loginModal .modal-content {
      width: 100%;
      box-sizing: border-box;
    }
  }
  
  /* For larger devices */
  @media (min-width: 768px) {
    #loginModal .modal-dialog {
      max-width: 500px; /* Or any fixed width you prefer */
      margin: auto;
      box-sizing: border-box;
    }
  }

  </style>
</head>

<body>

  <div style="background-color: antiquewhite;">
    <nav class="navbar navbar-light bg-white fixed-top shadow" style="flex-wrap: nowrap;">
      <div class="container-fluid d-flex justify-content-between align-items-center px-4"
        style="flex-wrap: nowrap; white-space: nowrap;">

        <!-- Left side: Logo & Title -->
        <div class="d-flex align-items-center" id="logoImg" style="white-space: nowrap;">
          <a href="/" title="Home" rel="home" class="d-flex align-items-center text-decoration-none"
            style="white-space: nowrap;">
            <img src="images/image.png" alt="Home" style="height: 50px; width: auto;">
            <span class="ms-2 fw-bold fs-5" style="white-space: nowrap;">
              <h5 style="color: black; margin: 0;">| GPD <span id="vanish" class="d-none d-md-inline">Inventory
                  Management System</span></h5>
            </span>
          </a>
        </div>

        <!-- Right Side: Login/Logout Button -->
        <div class="d-flex align-items-center" id="loginBtn" style="white-space: nowrap;">
          <?php
          if (!$loggedin) {
            echo '<button class="btn btn-outline" type="submit" name="login" id="loginButton">Login</button>';
          } else {
            echo '<a href="?logout=true" class="btn btn-outline-danger" type="button">Logout</a>';
            if ($role === 'admin') {
              echo '<span class="navbar-text ms-2 fw-bold">Admin</span>';
            } else {
              echo '<span class="navbar-text ms-2 fw-bold">User</span>';
            }
          }
          ?>
        </div>
      </div>
    </nav>
  </div>


<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true" style="padding: 0; margin: 0;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 0;">
      <div class="modal-body" id="loginModalBody">
        <!-- Login form content will be injected here -->
      </div>
    </div>
  </div>
</div>



  <!-- Hero Section -->
  <section id="hero" class="hero-section" data-aos="fade-right">
    <div class="container">
      <h1 class="display-4">Welcome to GPIMS</h1>
      <p class="lead">Note : This Website is only for GPD College Inventory Management.</p>
      <p class="lead">Access your dashboard securely and efficiently.</p>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="about-section" data-aos="fade-left">
    <div class="container">
      <h2>GPIMS : Goverment Polytechinc Inventory Management System</h2>
      <p>This Website is Designed to Change the Traditional way of Managing our college Inventory Record in Hard Copys
        to Change them In Digital Data. So the Access of Inventory Things Become easy to manage.</p>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="features-section" data-aos="fade-right">
    <div class="container">
      <h2 class="text-center">Features</h2>
      <div class="row">
        <div class="col-md-4" data-aos="fade-right">
          <div class="feature-box">
            <i class="fas fa-boxes fa-3x text-danger"></i>
            <h4 class="mt-3">Real-Time Tracking</h4>
            <p>Monitor inventory levels instantly and efficiently.</p>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up">
          <div class="feature-box">
            <i class="fas fa-chart-line fa-3x text-danger"></i>
            <h4 class="mt-3">Analytics &amp; Reporting</h4>
            <p>Get insightful analytics and reports to boost operations.</p>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
          <div class="feature-box">
            <i class="fas fa-lock fa-3x text-danger"></i>
            <h4 class="mt-3">Secure Access</h4>
            <p>Ensure data is safe with robust security measures.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Developer Section -->
  <section id="contact" class="contact-section" data-aos="fade-left">
    <div class="container">
      <h2>Contact Developer</h2>
      <p>For support or inquiries, please contact the developer:</p>
      <div class="contact-info"
        style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
        <a href="mailto:surajsinghch2055@gamil.com" title="Email"
          style="display: inline-flex; align-items: center; text-decoration: none; color: inherit;">
          <!-- Email SVG Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="32" height="32" viewBox="0 0 24 24">
            <path
              d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5v-2l8 5 8-5v2z" />
          </svg>
          <span style="margin-left: 10px;">surajsinghch2055@gamil.com</span>
        </a>
      </div>
      <div class="social-links" style="margin-top: 15px;">
        <a href="https://www.linkedin.com/in/suraj-singh-chauhan/" target="_blank" title="LinkedIn"
          style="margin-right: 15px;">
          <!-- LinkedIn SVG Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="32" height="32" viewBox="0 0 24 24">
            <path
              d="M19 0h-14c-2.757 0-5 2.243-5 5v14c0 2.757 2.243 5 5 5h14c2.757 0 5-2.243 5-5v-14c0-2.757-2.243-5-5-5zm-11 19h-3v-10h3v10zm-1.5-11.268c-.966 0-1.75-.8-1.75-1.732 0-.932.784-1.732 1.75-1.732s1.75.8 1.75 1.732c0 .932-.784 1.732-1.75 1.732zm13.5 11.268h-3v-5.604c0-1.336-.026-3.057-1.862-3.057-1.863 0-2.147 1.45-2.147 2.95v5.711h-3v-10h2.884v1.367h.041c.402-.762 1.38-1.563 2.84-1.563 3.037 0 3.6 2 3.6 4.599v5.597z" />
          </svg>
        </a>
        <a href="https://github.com/Suraj-1309" target="_blank" title="GitHub">
          <!-- GitHub SVG Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="32" height="32" viewBox="0 0 24 24">
            <path
              d="M12 .297c-6.63 0-12 5.373-12 12 0 5.302 3.438 9.8 8.205 11.387.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.415-4.042-1.415-.546-1.387-1.333-1.757-1.333-1.757-1.089-.744.084-.729.084-.729 1.205.084 1.84 1.236 1.84 1.236 1.07 1.835 2.807 1.305 3.495.998.108-.775.42-1.305.763-1.605-2.665-.3-5.467-1.332-5.467-5.931 0-1.31.468-2.381 1.235-3.221-.123-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.984-.399 3.003-.404 1.018.005 2.045.138 3.003.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.241 2.874.118 3.176.77.84 1.232 1.911 1.232 3.221 0 4.609-2.807 5.628-5.479 5.921.43.371.823 1.102.823 2.222 0 1.606-.015 2.896-.015 3.286 0 .321.216.694.825.576 4.765-1.588 8.2-6.086 8.2-11.386 0-6.627-5.373-12-12-12z" />
          </svg>
        </a>
      </div>
    </div>
  </section>



  <!-- Existing Login Container -->
  <div class="container" id="loginContainer"></div>

  <!-- Footer Section -->
  <footer class="footer-section" data-aos="fade-up">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> GPD Inventory Management System. All rights reserved.</p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <!-- AOS Library for scroll animations -->
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>
  <script>
document.addEventListener('DOMContentLoaded', function() {
  let logginFlag = false;

  // Minified HTML content for user login form
  let loginHTML = `<div class="card" style="border-radius:0;width:100%;"><div class="card-body p-4"><div class="pb-4 text-center"><img src="image.png" alt="college_logo" style="width:35%;height:auto;"></div><h3 class="text-uppercase text-center mb-4">Login to Inventory</h3><form action="index.php" method="POST"><div class="form-group"><input type="password" name="password" class="form-control form-control-lg" maxlength="15" placeholder="Password"></div><input type="hidden" name="loginType" value="user"><div class="d-flex justify-content-center"><button type="submit" class="btn btn-lg btn-primary myhover">Login</button></div><div class="text-right mt-2" id="loginadmin"><a href="#">Login as admin</a></div><p class="text-center text-muted mt-3">If you don’t have an account or can’t log in, please contact the admin.</p></form></div></div>`;

  // Minified HTML content for admin login form
  let adminLoginHTML = `<div class="card" style="border-radius:0;width:100%;"><div class="card-body p-4"><div class="pb-4 text-center"><img src="image.png" alt="college_logo" style="width:35%;height:auto;"></div><h3 class="text-uppercase text-center mb-4">Logging In As Admin</h3><form action="index.php" method="POST"><div class="form-group"><input type="password" name="password" class="form-control form-control-lg" maxlength="15" placeholder="Password"></div><input type="hidden" name="loginType" value="admin"><div class="d-flex justify-content-center"><button type="submit" class="btn btn-lg btn-primary myhover">Login</button></div><div class="text-right mt-2" id="loginuser"><a href="#">Login as User</a></div></form></div></div>`;

  // Attach event for toggling to admin login form
  function attachAdminLoginEvent() {
    var adminLink = document.getElementById('loginadmin');
    if (adminLink) {
      adminLink.addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('loginModalBody').innerHTML = adminLoginHTML;
        attachUserLoginEvent();
      });
    }
  }

  // Attach event for toggling to user login form
  function attachUserLoginEvent() {
    var userLink = document.getElementById('loginuser');
    if (userLink) {
      userLink.addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('loginModalBody').innerHTML = loginHTML;
        attachAdminLoginEvent();
      });
    }
  }

  // When the login button is clicked, toggle the modal
  document.getElementById('loginButton').onclick = function(event) {
    event.preventDefault();
    if (logginFlag) {
      $('#loginModal').modal('hide');
    } else {
      document.getElementById('loginModalBody').innerHTML = loginHTML;
      attachAdminLoginEvent();
      $('#loginModal').modal('show');
    }
    logginFlag = !logginFlag;
  };
});
</script>

</body>

</html>