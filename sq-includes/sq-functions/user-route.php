<?php 


// Define a reusable callback function
function createMenuCallback($menuName, $routePath, $callback) {
    global $sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name;
    
    return function () use ($menuName, $routePath, $callback, $sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name) {
        // db is connected
        if (isSQ_DBConnected($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name)) {
            echo "Menu: $menuName<br>";
            echo "Route: $routePath<br>";
            $callback();
        } else {
            // db is not connected
            header("Location: /squehub-innitial-setup/get-started");
            exit;
        }
    };
}



// Example usage of the reusable callback
$router->sq_addRoute('/', createMenuCallback('Home', '/', function () {
    echo 'Home Page';
}));

$router->sq_addRoute('/about', createMenuCallback('About', '/about', function () {
    echo 'About Page';
}));


// Define a reusable callback function for configuration route
function createConfigCallback($pageName, $routePath, $callback) {
    global $sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name;
    
    return function () use ($pageName, $routePath, $callback, $sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name) {
        // Additional functionality before executing the callback
        // ...

        // db is connected
        if (isSQ_DBConnected($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name)) {

            header("Location: /");

            // Additional functionality after executing the callback
            // ...
        } else {
            // db is not connected
            renderInnitialSetupHTMLStructureStart();
           // echo "Menu: $pageName<br>";
           // echo "Route: $routePath<br>";
            $callback();
            renderInnitialSetupHTMLStructureEnd();
        }
    };
}


// Use the reusable callback for the /squehub-innitial-setup route
$router->sq_addRoute('/squehub-innitial-setup/get-started', createConfigCallback('Configuration', '/squehub-innitial-setup/get-started', function () {
    // Additional functionality specific to the /squehub-innitial-setup route
    renderInnitialSetupHTMLStructurebody();
}));

// Use the reusable callback for the /squehub-innitial-setup route
$router->sq_addRoute('/squehub-innitial-setup/install', createConfigCallback('Install', '/squehub-innitial-setup/get-started', function () {
    // Additional functionality specific to the /squehub-innitial-setup route
    renderSetupInstallHTMLStructurebody();
}));




$router->sq_addRoute('/logout', function () {
    // Logout
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: /");
});


/*
$router->sq_addRoute('/', function () {
    global $sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name;
    // db is connected
    if (isSQ_DBConnected($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name)) {
        echo 'Home Page';
    } else {
        // db is not connected
        header("Location: /sq-configuration");
        exit;
    }
});

$router->sq_addRoute('/h2', function () {
    echo "Home Page 2";
});


$router->sq_addRoute('/sq-configuration', function () {
    global $sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name;
    // db is connected
    if (isSQ_DBConnected($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name)) {
        header("Location: /");
    } else {
        // db is not connected
        echo 'configuration Page';
        exit;
    }
});




$router->sq_addRoute('/register', function () {
    echo "Register Page";
});

$loginFileExist = false; // Assuming login file exists

$router->sq_addRoute('/login', function (){
    global $loginFileExist;
    if ($loginFileExist) {
        echo "Local Login Page"; // Display default login

    } else {
        echo "Default Login Page"; // Display default login
    }
});


$router->sq_addRoute('/logout', function () {
    // Logout
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: /");
});

$router->sq_addRoute('/contact', function () {
    echo "Contact us at contact@example.com.";
});

$router->sq_addRoute('/user/(\d+)', function ($params) {
    $userId = $params[1];
    echo "User Profile Page for User ID $userId";
});

$router->sq_addRoute('(\d+)', function ($params) {
    $postId = $params[1];
    echo "Post Page for Post ID $postId";
});



$router->sq_addRoute('/article/(\w+)', function ($params) {
    $slug = $params[1];
    echo "Article Page for Slug: $slug";
});

$router->sq_addRoute('/(\d+)', function ($params) {
    $userId = $params[1];
    echo "Custom User ID $userId";
});


$router->sq_addRoute('/(\w+)', function ($params) {
    $slug = $params[1];
    echo "Custom Slug: $slug";
}); 



*/