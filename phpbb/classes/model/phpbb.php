<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Phpbb extends Model_Database
{
	protected $_db_config_group;

	public function __construct($db_config_group)
	{
		$this->_db_config_group = $db_config_group;
	}

	public function get_user_by_id($user_id)
	{
		return DB::select()
				 ->from('users')
				 ->where('user_id', '=', $user_id)
				 ->limit(1)
				 ->execute($this->_db_config_group)
                 ->as_row();
	}

	public function get_user_by_username($username)
    {
    	return DB::select()
    			 ->from('users')
    			 ->where('username', '=', $username)
    			 ->limit(1)
    			 ->execute($this->_db_config_group)
                 ->as_array();
    }

    public function get_user_group_membership($user_id)
    {
    	$result =  DB::select('groups.group_name')
    				 ->from('groups')
                     ->join('user_group')
                     ->on('groups.group_id', '=', 'user_group.group_id')
    				 ->where('user_group.user_id', '=', $user_id)
    				 ->execute($this->_db_config_group)
                     ->as_array();

    	foreach ($result as $group)
    	{
    		$groups[] = $group['group_name'];
    	}

    	return $groups;
    }
}