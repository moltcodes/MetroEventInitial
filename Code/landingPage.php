<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <link rel='stylesheet' href='stylesheet.css'>
</head>

<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100 ">
      <div class="col-md-9 col-lg-8 col-xl-6">
        <img src="../Resources/Asset9.png" class="img-fluid" alt="Loading image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-3 offset-xl-1">
        <br><h3><b>Welcome to Metro Events</b></h3><br>
        <form method = "POST">
          <!-- Email input -->
          <div class="form-outline mb-4">
            <input type="name" id="form3Example3" class="form-control form-control-lg"
              placeholder="Enter a valid email address" name = "loginEmail" />
            <label class="form-label" for="form3Example3">Email address</label>
          </div>

          <!-- Password input -->
          <div class="form-outline mb-3">
            <input type="password" id="form3Example4" class="form-control form-control-lg"
              placeholder="Enter password" name = "loginPassword"/>
            <label class="form-label" for="form3Example4">Password</label>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <!-- Checkbox -->
            <div class="form-check mb-0">
              <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
              <label class="form-check-label" for="form2Example3">
                Remember me
              </label>
            </div>
            <a href="#!" class="text-body">Forgot password?</a>
          </div>

          <div class="text-center text-lg-start mt-4 pt-2">
            <input type="submit" class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;" value = "Log-in"></input>
            <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="registerPage.php"
                class="link-danger">Register</a></p>
          </div>

        </form>
      </div>
    </div>
  </div>
    <!-- Right -->
  </div>
</section>


<?php
    include("index.php");
    include_once 'api.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginEmail']) && isset($_POST['loginPassword'])) {
      $email = $_POST['loginEmail'];
      $password = $_POST['loginPassword'];
      if(!$email || !$password){
          echo "<script>alert('Error in log-in!')</script>";
          return;
      }
      loginUser($email, $password);
  }

?>