<?php
/**
 * index.php class file.
 * 
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 */

include('Controller.php');
$controller = new Controller($_REQUEST);
$action = isset($_GET['action']) ? 'action' . ucfirst($_GET['action']) : 'actionIndex';
if (method_exists($controller, $action)) {
    $controller->$action();
}
