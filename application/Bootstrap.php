<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->throwExceptions(false);

        $front->registerPlugin(new Obj_System_VirtualControllerPlugin());
    }
	

   
    protected function _initConstants() {
    	//User types
    	include APPLICATION_PATH.'/constants/user-types.php';
    	//System
    }
	
	 protected function _initRouter()
  {

		$router = Zend_Controller_Front::getInstance()->getRouter();
		
		$router->addRoute('entity_page',
            new Zend_Controller_Router_Route_Regex('post/(\d+)(?:/(.*))?',
                array(
                    'controller' => 'index',
                    'action' => 'post'
                ),
                array(
                    1 => 'id',
                    2 => 'title'
                ),
                'post/%s/%s/'
            )
        );
		
	
		
		$router->addRoute('iventor',
            new Zend_Controller_Router_Route_Regex('iventor/(\d+)(?:/(.*))?',
                array(
                    'controller' => 'iventor',
                    'action' => 'index',
					'ID' => null
                ),
                array(
                    1 => 'ID',
                    2 => 'title',

                ),
                'iventor/%s/%s/'
            )
        );
		
		
		/*$router->addRoute('catalog',
            new Zend_Controller_Router_Route_Regex('catalog/(\d+)(?:/(.*))?',
                array(
                    'controller' => 'catalog',
                    'action' => 'index'

					
					
                ),
                array(
					1 => 'title'

                ),
                'catalog/%s/'
            )
        );*/
		
		
		$route=new Zend_Controller_Router_Route(
		   'catalog/:title/:cityname/:page/',
		   array(
			  'controller'  => 'catalog',
			  'action'      => 'index',
			  'title' => 'default',
			  'cityname' => 'all',
			  'page' => '1'
			 
		   )
		);

		$router->addRoute('catalog',$route);
		
		
		
		
		
		
  }

}

