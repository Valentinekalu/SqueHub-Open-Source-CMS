<?php 
$currentURL = $_SERVER["REQUEST_URI"];
$pageName = pathinfo($currentURL, PATHINFO_FILENAME);

// Remove any hyphens
$pageName = str_replace('-', ' ', $pageName);




/**
 * Render the initial HTML structure for the setup page.
 * This includes the doctype, metadata, CSS files, and opening body tag.
 */
function renderInnitialSetupHTMLStructureStart() {
    echo '<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
    <head> ';

    include __DIR__ . '/init/innitialsetup-head.php';

 echo '   
 </head>
 <body  class=" d-flex flex-column">
     <script src="../sq-admin/assets/js/demo-theme.min.js?1684106062"></script>
        <div class="page">';
}

/**
 * Render the body HTML structure for the setup page.
 * This includes   body tag, including the modal.php file, and adding JavaScript files.
 */
function renderInnitialSetupHTMLStructurebody() {
    echo '
    <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
      </div>
      <div class="card card-md">
        <div class="card-body text-center py-4 p-sm-5">
          <img src="./static/illustrations/undraw_sign_in_e6hj.svg" height="120" class="mb-n2" alt="">
          <h1 class="mt-5">Welcome to SqueHub CMS!</h1>
          <p class="text-muted">SqueHub CMS is a powerful and flexible content management system designed to empower developers like you. With a robust set of features and a user-friendly interface, SqueHub allows you to create dynamic websites with ease.</p>
        
          <div class="hr-text hr-text-center hr-text-spaceless m-3"></div>

          <p class="text-muted">Explore the comprehensive suite of tools and unleash your creativity. Whether you are building a personal blog or a full-fledged e-commerce platform, SqueHub has got you covered.</p>
          <p class="text-muted">Let us embark on this journey together and create something amazing!</p>
          
        </div>


      </div>
      <div class="row align-items-center mt-3">
      <div class="card-body">
        <ul class="steps steps-green steps-counter my-4">
          <li class="step-item active">Get Started</li>
          <li class="step-item">Billing Information</li>
          <li class="step-item">Confirmation</li>
        </ul>
      </div>
        <div class="col">
          <div class="btn-list justify-content-end">
            <a href="#" class="btn btn-link link-secondary">
              Set up later
            </a>
            <a href="/squehub-innitial-setup/install" class="btn btn-primary">
              Continue
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>';
}


/**
 * Render the body HTML structure for the setup page.
 * This includes   body tag, including the modal.php file, and adding JavaScript files.
 */
function renderSetupInstallHTMLStructurebody() {
  global $sq_dbc;
  global $generateTemporaryUrl;
  $errorMsg = '';
  $successMsg = '';
  $connSuccessMsg = ''; // Added connection success message
  $connErrorMsg = ''; // Added connection error message
  
  session_start(); // Start a session
  
  // Check if the form has been submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sq_db_servername = $_POST['sq_db_servername'];
      $sq_db_username = $_POST['sq_db_username'];
      $sq_db_password = $_POST['sq_db_password'];
      $sq_db_name = $_POST['sq_db_name'];
  
      $installation = new sq_CMS_Installation($sq_dbc);
  
      // Try to store the database details
      $result = $installation->storeDatabaseDetails($sq_db_servername, $sq_db_name, $sq_db_username, $sq_db_password);
  
      if ($result === 'Database details saved successfully!') {
          $successMsg = $result;
          
          // Generate temporary URL
          $token = bin2hex(random_bytes(16));
          $generateTemporaryUrl = "{$token}";
  
          // Store URL in session
          $_SESSION['temporary_url'] = $generateTemporaryUrl;
  
          // Check if database connection is successful
          $connResult = $installation->checkDatabaseConnection($sq_db_servername, $sq_db_name, $sq_db_username, $sq_db_password);
          
          if (strpos($connResult, 'Error') !== 0) {
              $connSuccessMsg = $connResult; // Set connection success message
                        // Redirect after 3 seconds
          echo '<script>
          setTimeout(function() {
              window.location.href = "'.$generateTemporaryUrl.'";
          }, 3000);
        </script>';
          } else {
              $connErrorMsg = $connResult; // Set connection error message
          }
      } else {
          $errorMsg = 'Error saving database detail. ' . $result;
      }
  }
  
  echo '
  <div class="page page-center">
      <div class="container container-tight py-4">
          <div class="text-center mb-4">
              <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
          </div>';
  

  
  // Display error or success messages
  if ($errorMsg !== '') {
      echo '<div class="alert alert-danger" role="alert">' . $errorMsg . '</div>';
  }
  
  if ($successMsg !== '') {
      echo '<div class="alert alert-success" role="alert">' . $successMsg . '</div>';
  }


    // Display connection success or error messages
    if ($connSuccessMsg !== '') {
      echo '<div class="alert alert-success" role="alert">' . $connSuccessMsg . '</div>';
  }
  
  if ($connErrorMsg !== '') {
      echo '<div class="alert alert-danger" role="alert">' . $connErrorMsg . '</div>';
  }
  
  // Display messages here
  if (isset($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name)) {
      $installation->checkDatabaseConnection($sq_db_servername, $sq_db_name, $sq_db_username, $sq_db_password);
  }
  
  echo '
          <label class="form-label text-center">Input Your Database Details for Easy Installation!</label>
          <form action="" method="post">
              <fieldset class="form-fieldset">
                  <div class="mb-3">
                      <label class="form-label required">Your Database Host</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_servername" required/>
                  </div>
                  <div class="mb-3">
                      <label class="form-label required">Your Database Username</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_username" required/>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Your Database Password</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_password"/>
                  </div>
  
                  <div class="mb-3">
                      <label class="form-label required">Your Database Name</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_name" required/>
                  </div>
              </fieldset>
              <div class="row align-items-center mt-3">
              <div class="card-body">
                <ul class="steps steps-green steps-counter my-4">
                  <li class="step-item">Get Started</li>
                  <li class="step-item active">Install</li>
                  <li class="step-item">Final Config</li>
                </ul>
              </div>
              
              </div>
              <div class="row align-items-center mt-3">
                  <div class="col-4">
                  <a href="/squehub-innitial-setup/get-started" class="btn btn-link link-secondary">
                  Go Back
                </a>
                  </div>
                  <div class="col">
                      <div class="btn-list justify-content-end">
                          <button type="submit" class="btn btn-primary">Install</button>
                      </div>
                  </div>
              </div>
          </form>
      </div>
  </div>';
}



/**
 * Render the body HTML structure for the setup page.
 * This includes   body tag, including the modal.php file, and adding JavaScript files.
 */
function renderSetupConfigHTMLStructurebody() {

  session_start(); // Start a session
  
  // Check if the form has been submitted

  echo '
  <div class="page page-center">
      <div class="container container-tight py-4">
          <div class="text-center mb-4">
              <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
          </div>';
  

  

  
  echo '
          <label class="form-label text-center">Create Your Admin Credentials and Finish The Setup!</label>
          <form action="" method="post">
              <fieldset class="form-fieldset">
                  <div class="mb-3">
                      <label class="form-label required">Your Database Host</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_servername" required/>
                  </div>
                  <div class="mb-3">
                      <label class="form-label required">Your Database Username</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_username" required/>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Your Database Password</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_password"/>
                  </div>
  
                  <div class="mb-3">
                      <label class="form-label required">Your Database Name</label>
                      <input type="text" class="form-control" autocomplete="off" name="sq_db_name" required/>
                  </div>
              </fieldset>
              <div class="row align-items-center mt-3">
              <div class="card-body">
                <ul class="steps steps-green steps-counter my-4">
                  <li class="step-item">Get Started</li>
                  <li class="step-item active">Install</li>
                  <li class="step-item">Final Config</li>
                </ul>
              </div>
              
              </div>
              <div class="row align-items-center mt-3">
                  <div class="col-4">
                  <a href="/squehub-innitial-setup/get-started" class="btn btn-link link-secondary">
                  Go Back
                </a>
                  </div>
                  <div class="col">
                      <div class="btn-list justify-content-end">
                          <button type="submit" class="btn btn-primary">Install</button>
                      </div>
                  </div>
              </div>
          </form>
      </div>
  </div>';
}



$router->sq_addRoute('/squehub-innitial-setup/' .  $_SESSION['temporary_url'], function () {
  renderInnitialSetupHTMLStructureStart();
  renderSetupConfigHTMLStructurebody();
  renderInnitialSetupHTMLStructureEnd();
});




/**
 * Render the closing HTML structure for the setup page.
 * This includes closing the body tag, including the modal.php file, and adding JavaScript files.
 */
function renderInnitialSetupHTMLStructureEnd() {
    $var = 'sks'; // Define your variable here
    echo '
        <a href="' . $var . '"></a>

        <!-- Libs JS -->
        <script src="../sq-admin/assets/libs/apexcharts/dist/apexcharts.min.js?1684106062" defer></script>
        <script src="../sq-admin/assets/libs/jsvectormap/dist/js/jsvectormap.min.js?1684106062" defer></script>
        <script src="../sq-admin/assets/libs/jsvectormap/dist/maps/world.js?1684106062" defer></script>
        <script src="../sq-admin/assets/libs/jsvectormap/dist/maps/world-merc.js?1684106062" defer></script>
        <!-- Tabler Core -->
        <script src="../sq-admin/assets/js/tabler.min.js?1684106062" defer></script>
        <script src="../sq-admin/assets/js/demo.min.js?1684106062" defer></script>
        <script src="../sq-admin/assets/js/custom.js"></script>

    </body>
</html>';
}
