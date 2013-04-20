<?php

/* Loginza Auth Adapter
   (c) sleptor .dot. gmail .dot. com
*/

class Zend_Auth_Adapter_Loginza implements Zend_Auth_Adapter_Interface
{
    protected $_token = null;

    /**
     * __construct() - Sets configuration options
     * @param  string                   $token
     * @return void
     */
    public function __construct($token)
    {
        $this->_token = $token;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
    	$LoginzaAPI = new LoginzaAPI();
		
		// получаем профиль авторизованного пользователя
		$UserProfile = $LoginzaAPI->getAuthInfo ( $this->_token );
		
		// проверка на ошибки
		if (! empty ( $UserProfile->error_type )) {
			// есть ошибки, выводим их
			$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE;
			$this->_authenticateResultInfo['messages'][] = $UserProfile->error_type . ": " . $UserProfile->error_message; 
		} elseif (empty ( $UserProfile )) {
			// прочие ошибки
			$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE;
			$this->_authenticateResultInfo['messages'][] = 'Temporary error'; 
		} else {
			$this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
        	$this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
        	
        	$LoginzaProfile = new LoginzaUserProfile($UserProfile);
        	        	
        	$this->_authenticateResultInfo['identity'] = $LoginzaProfile;
		}
		         
        return $this->_authenticateCreateAuthResult();
    }

    /**
     * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    
    protected function _authenticateCreateAuthResult()
    {
        return new Zend_Auth_Result(
            $this->_authenticateResultInfo['code'],
            $this->_authenticateResultInfo['identity'],
            $this->_authenticateResultInfo['messages']
        );
    }
}
