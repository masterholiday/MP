<?php
class Obj_System_VirtualControllerPlugin extends Zend_Controller_Plugin_Abstract
{
	
	public function routeShutdown(Zend_Controller_Request_Abstract &$request)
    {

    	$front = Zend_Controller_Front::getInstance();
    	if (!$front->getDispatcher()->isDispatchable($request)) {
            header('Location: /404.html');
            die();
    	}
        else {
            $class  = $front->getDispatcher()->loadClass($front->getDispatcher()->getControllerClass($request));
            $method = $front->getDispatcher()->formatActionName($request->getActionName());
            if (!is_callable(array($class, $method))) {
                header('Location: /404.html');
                die();

            }
        }
    }

}

