<style>
  nav {
    border-bottom: 5px solid red;
    box-shadow: 0px 5px 5px -5px rgba(0, 0, 0, 1);
    box-sizing: border-box;
  }

  /* Responsive adjustments */
  /* Responsive adjustments */
  @media (max-width: 767px) {

    nav {
      width: 100vw;
    }

    nav #logoImg {
      position: relative;
      left: -12%;
      padding: 0%;
      margin: 0%;
      margin-left: -25;
    }

    nav #loginBtn {
      position: absolute;
      right: 5%;
    }

    #vanish {
      display: none;
    }
  }
</style>


<div style="background-color: antiquewhite;">
  <nav class="navbar navbar-light bg-white fixed-top shadow" style="flex-wrap: nowrap;">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4"
      style="flex-wrap: nowrap; white-space: nowrap;">

      <!-- Left side: Logo & Title -->
      <div class="d-flex align-items-center" id="logoImg" style="white-space: nowrap;">
        <a href="/" title="Home" rel="home" class="d-flex align-items-center text-decoration-none"
          style="white-space: nowrap;">
          <img src="../images/image.webp" alt="Home" style="height: 50px; width: auto;" loading="lazy">
          <span class="ms-2 fw-bold fs-5" style="white-space: nowrap;">
            <h5 style="color: black; margin: 0;">| GPD <span id="vanish" class="d-none d-md-inline">Inventory Management
                System</span></h5>
          </span>
        </a>
      </div>

      <!-- Right Side: Display Session Name or Login Button -->
      <div class="d-flex align-items-center" id="loginBtn" style="white-space: nowrap;">
        <?php
        if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
          // If session name exists, display it
          echo '<span class="navbar-text ms-2 fw-bold" style="color: black">' . htmlspecialchars($_SESSION['name']) . '</span>';
        } else {
          // Otherwise, display the login button
          echo '<button class="btn btn-outline" type="submit" name="login" id="loginButton">Login</button>';
        }
        ?>
      </div>

    </div>
  </nav>
</div>