<?php 

// Define a reusable callback function
function createAdminCallback($menuName, $routePath, $callback) {
    global $servername, $username, $password, $dbname;
    
    return function () use ($menuName, $routePath, $callback, $servername, $username, $password, $dbname) {
        // Additional functionality before executing the callback
        // ...

        // db is connected
        if (isSQ_DBConnected($servername, $username, $password, $dbname)) {
            echo "Admin Menu: $menuName<br>";
            echo "Route: $routePath<br>";
            $callback();

            // Additional functionality after executing the callback
            // ...
        } else {
            // db is not connected
            header("Location: /sq-configuration");
            exit;
        }
    };
}

// Use the reusable callback for admin routes
$router->sq_addRoute($router->sq_getAdminUrl(), createAdminCallback('Admin Control Panel', $router->sq_getAdminUrl(), function () {
    echo "Welcome to the Admin Control Panel";
}));

$router->sq_addRoute($router->sq_getAdminUrl() . '/settings', createAdminCallback('Admin Settings', $router->sq_getAdminUrl() . '/settings', function () {
    echo "Admin Settings Page";
}));



/*
// Add a route for the admin control panel
$router->sq_addRoute($router->sq_getAdminUrl(), function () {
        echo "Welcome to the Admin Control Panel";

});

// Add other admin routes with their respective callbacks
$router->sq_addRoute($router->sq_getAdminUrl() . '/settings', function () {
    echo "Admin Settings Page";
});

*/

?>
