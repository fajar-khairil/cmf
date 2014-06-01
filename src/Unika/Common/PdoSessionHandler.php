<?php
/**
 *	This file is part of the Unika-CMF project.
 *	extend Symfony PdoSessionHandler to add session info table	
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

 namespace Unika\Common;

 class PdoSessionHandler extends \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
 {

 	protected $session_info_table;
 	protected $app;

     /**
     * Constructor.
     *
     * List of available options:
     *  * db_table: The name of the table [required]
     *  * db_id_col: The column where to store the session id [default: sess_id]
     *  * db_data_col: The column where to store the session data [default: sess_data]
     *  * db_time_col: The column where to store the timestamp [default: sess_time]
     *
     * @param \PDO  $pdo       A \PDO instance
     * @param array $dbOptions An associative array of DB options
     *
     * @throws \InvalidArgumentException When "db_table" option is not provided
     */
    public function __construct(\PDO $pdo, array $dbOptions = array())
    {
    	parent::__construct();
    	$this->app = \Unika\Bag::instance();
    	$this->session_info_table = $this->app['config']['session.Database.session_info.table'];
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
		parent::write($sessionId,$data);

		//get current request
		/*$crequest = $this->app['request_stack']->getCurrentRequest();

        $insertStmt = $this->pdo->prepare(
            "INSERT INTO session_info (session_token,user_agent,ip_address,remember_token, session_time) 
            VALUES (:session_token,:user_agent,:ip_address,:remember_token,:session_time)"
        );

        $remember_cookie_name = $this->app['config'][ 'cookie.'.$this->app['config']['auth.cookie_remember'] ];

        $insertStmt->bindParam(':session_token', $sessionId, \PDO::PARAM_STR);
        $insertStmt->bindParam(':user_agent', $crquest->headers->get('HTTP_USER_AGENT','Unknown'), \PDO::PARAM_STR);
        $insertStmt->bindParam(':ip_address', $crquest->getClientIp(), \PDO::PARAM_STR);
        $insertStmt->bindParam(':remember_token', $crquest->cookies->get( $remember_cookie_name ), \PDO::PARAM_STR);
        $insertStmt->bindValue(':session_time', time(), \PDO::PARAM_INT);
        $insertStmt->execute();*/
    }

     /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
    	parent::destroy($sessionId);

        // delete the record associated with this id
        $sql = "DELETE FROM $this->session_info_table WHERE session_token = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete a session: %s', $e->getMessage()), 0, $e);
        }

        return true;  
    }	

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
    	parent::gc($maxlifetime);

        // delete the session records that have expired
        $sql = "DELETE FROM $this->session_info_table WHERE session_time < :time";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':time', time() - $maxlifetime, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete expired sessions: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }
 }