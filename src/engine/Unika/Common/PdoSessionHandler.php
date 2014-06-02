<?php
/**
 *	This file is part of the Unika-CMF project.
 *	extend Symfony PdoSessionHandler to add session info table	
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

 namespace Unika\Common;

 class PdoSessionHandler extends \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler
 {

 	protected $session_info_table;

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
    	parent::__construct($pdo,$dbOptions);
    	$app = \Application::instance();
    	$this->session_info_table = $this->app['config']['session.Database.session_info.table'];
    	unset($app);
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