<?php
class Stats
{
    public static function registerVisit()
    {
        $sql = "INSERT INTO visitors (day, ip, hits) VALUES (CURRENT_DATE, :ip, 1) ON DUPLICATE KEY UPDATE hits=hits+1;";
        $statement = Database::getInstance()->prepare($sql);
		$ip2long = ip2long($_SERVER['REMOTE_ADDR']);
        $statement->bindParam(':ip', $ip2long, PDO::PARAM_INT);
        return $statement->execute();
    }
	
	/**
	 * @return array
	 */
	public static function getUsers()
	{
		$sql = "
			(SELECT 'total' AS stat_key, COUNT(*) AS stat_value FROM users)
				UNION 
			(SELECT 'today' AS stat_key, COUNT(*) AS stat_value FROM users WHERE DATE(registered_at)=DATE(NOW()) )
				UNION 
			(SELECT 'yesterday' AS stat_key, COUNT(*) AS stat_value FROM users WHERE DATE(registered_at)=DATE(NOW() - INTERVAL 1 DAY) )
		";
			
		$statement = Database::getInstance()->query($sql);
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$return = array();
		
		foreach ($result as $row) {
			$return[$row['stat_key']] = $row['stat_value'];
		}
		
		$return['growth'] = ($return['yesterday'] == 0) ? 
			($return['today'] * 100) : round($return['today']/$return['yesterday'], 0); 
		
		return $return;
	}
	
	/**
	 * @return array
	 */
	public static function getVisitors()
	{
		$sql = "
			(SELECT 'total' AS stat_key, COUNT(*) AS visitors, SUM(hits) AS hits FROM visitors)
				UNION 
			(SELECT 'today' AS stat_key, COUNT(*) AS visitors, SUM(hits) AS hits FROM visitors WHERE DAY=DATE(NOW()))
				UNION 
			(SELECT 'yesterday' AS stat_key, COUNT(*) AS visitors, SUM(hits) AS hits FROM visitors WHERE DAY=DATE(NOW() - INTERVAL 1 DAY))
				UNION 
			(SELECT 'this_week' AS stat_key, COUNT(*) AS visitors, SUM(hits) AS hits FROM visitors WHERE DAY>=DATE(NOW() - INTERVAL 1 WEEK))
				UNION 
			(SELECT 'last_week' AS stat_key, COUNT(*) AS visitors, SUM(hits) AS hits FROM visitors WHERE DAY<DATE(NOW() - INTERVAL 1 WEEK) AND DAY>=DATE(NOW() - INTERVAL 2 WEEK))
		";
		
		$statement = Database::getInstance()->query($sql);
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$return = array();
		
		foreach ($result as $row) {
			$return[$row['stat_key']] = array(
				'visitors' => $row['visitors'],
				'hits' => $row['hits'],
			);
		}
		
		$return['today']['diff_percent'] = ($return['yesterday']['visitors'] == 0) ? 
			($return['today']['visitors'] * 100) : round($return['today']['visitors']/$return['yesterday']['visitors'], 0);
		$return['this_week']['diff_percent'] = ($return['last_week']['visitors'] == 0) ? 
			($return['this_week']['visitors'] * 100) : round($return['this_week']['visitors']/$return['last_week']['visitors'], 0); 
		
		return $return;
	}
}
