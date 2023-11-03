<?php
//sq-includes/sq-classes/sq-Router.php
/**
 * @package SqueHub
 * @subpackage Router
 */

/**
 * Class sq_Router
 * 
 * location: sq-includes/sq-classes/sq-Router.php
 *
 * This class handles routing in SqueHub CMS. It provides functionality to add routes,
 * set maintenance mode, define admin URLs, and route requests.
 *
 * @package SqueHub
 * @subpackage Router
 */
class sq_Router {
    /**
     * @var array $routes Holds registered routes and their corresponding callbacks.
     */
    private $routes = [];

    /**
     * @var int $maintenanceMode Indicates whether maintenance mode is enabled (1 for on, 0 for off).
     *                          This value is determined by the global variable $ghostmood.
     */
    private $maintenanceMode;

    /**
     * @var string $adminUrl The default admin URL. This value is determined by the global variable $getadminurl.
     */
    private $adminUrl;

    /**
     * Constructor initializes Router with maintenance mode and admin URL.
     */
    public function __construct() {
        global $ghostmood; // Access the global variable
        global $getadminurl;

        // Set maintenance mode and admin URL
        $this->maintenanceMode = $ghostmood;
        $this->adminUrl = $getadminurl;

        // Initialize default routes, including the admin route
        $this->sq_initRoutes();
    }

    /**
     * Add a new route to SqueHub CMS.
     *
     * @param string $path The route path.
     * @param callable $callback The callback function for the route.
     */
    public function sq_addRoute($path, $callback) {
        $this->routes[$path] = $callback;
    }

    /**
     * Set maintenance mode in SqueHub CMS.
     *
     * @param int $mode The maintenance mode (1 for on, 0 for off).
     */
    public function sq_setMaintenanceMode($mode) {
        $this->maintenanceMode = $mode;
    }

    /**
     * Set the admin URL in SqueHub CMS.
     *
     * @param string $url The admin URL.
     */
    public function sq_setAdminUrl($url) {
        $this->adminUrl = $url;
        $this->sq_initRoutes(); // Reinitialize routes with the new admin URL
    }

    /**
     * Get the admin URL in SqueHub CMS.
     *
     * @return string The admin URL.
     */
    public function sq_getAdminUrl() {
        return $this->adminUrl;
    }

    

    /**
     * Route the request in SqueHub CMS.
     */
    public function sq_route() {
        $request_uri = rtrim($_SERVER['REQUEST_URI'], '/'); // Remove trailing slash

        if (!$this->maintenanceMode || strpos($request_uri, $this->adminUrl) === 0) {
            // If maintenance mode is off or the request URI starts with the admin URL, don't apply maintenance mode
            foreach ($this->routes as $path => $callback) {
                $pattern = str_replace('/', '\/', $path);
                $pattern = preg_replace('/(:\w+)/', '([^\/]+)', $pattern);
                $pattern = str_replace('...', '(.+)', $pattern);

                // Make trailing slash optional in the route pattern
                $pattern = '~^' . rtrim($pattern, '/') . '/?$~';

                if (preg_match($pattern, $request_uri, $matches)) {
                    // Pass matched parameters to the callback function
                    $this->sq_executeCallback($callback, $matches);
                    return;
                }
            }

            if (strpos($request_uri, $this->adminUrl) === 0) {
                // If the request URI starts with the admin URL, display admin-specific 404 error
                $this->sq_adminNotFound();
            } else {
                // If no route is found and it's not an admin URL, display a regular 404 error
                $this->sq_notFound();
            }
        } else {
            // Maintenance mode is on, and the request is not an admin URL
            $this->sq_maintenanceMode();
        }
    }

    /**
     * Execute the callback function in SqueHub CMS.
     *
     * @param callable $callback The callback function.
     * @param array $params The matched parameters.
     */
    private function sq_executeCallback($callback, $params) {
        if (is_callable($callback)) {
            $callback($params);
        }
    }

    /**
     * Handle maintenance mode in SqueHub CMS.
     */
    private function sq_maintenanceMode() {
        header("HTTP/1.0 503 Service Unavailable");
        echo "Maintenance Mode is ON";
    }

    /**
     * Handle a regular 404 error in SqueHub CMS.
     */
    private function sq_notFound() {
        header("HTTP/1.0 404 Not Found");
        echo "404 Page Not Found";
    }

    /**
     * Handle an admin-specific 404 error in SqueHub CMS.
     */
    private function sq_adminNotFound() {
        header("HTTP/1.0 404 Not Found");
        echo "Admin 404 Not Found";
    }

    /**
     * Initialize default routes, including the admin route in SqueHub CMS.
     */
    private function sq_initRoutes() {
        $this->routes = [
            $this->adminUrl => null, // Placeholder, as we don't need a callback here
        ];
    }
}
?>
