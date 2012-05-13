<?php defined('SYSPATH') or die('No direct script access.');

/**
* Kohana phpBB3 Library
*
* Kohana phpBB3 bridge (access phpBB3 user sessions and other functions inside your Kohana applications).
* Original author is Tomaž Muraus (http://www.tomaz-muraus.info).
*
* @author Almir Sarajcic
* @version 1.0
* @link https://github.com/almirsarajcic/phpBB3-library-for-Kohana-Framework
*/
class Phpbb {

    protected $_phpbb, $_user;

    /**
     * Constructor.
     */
    public function __construct()
    {
        global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template;
        
        $config = Kohana::$config->load('phpbb');

        $this->_phpbb = new Model_Phpbb($config->get('db_config_group'));

		define('IN_PHPBB', TRUE);
		define('FORUM_ROOT_PATH', $config->get('phpbb_path'));

		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : FORUM_ROOT_PATH;
		$phpEx = substr(strrchr(__FILE__, '.'), 1);

		// Include needed files
		include($phpbb_root_path . 'common.' . $phpEx);
		include($phpbb_root_path . 'config.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

		// Initialize phpBB user session
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup();

		// Save user data into $_user variable
		$this->_user = $user;
    }

    /**
     * Returns user data array.
     * @return array User information
     */
    public function get_user()
    {
        return $this->_user->data;
    }

    /**
     * Returns information from the user data array.
     * @param  string $key Item key
     * @return mixed       User information on success, FALSE otherwise.
     */
    public function get_user_info($key)
    {
        if (array_key_exists($key, $this->_user->data))
        {
            return $this->_user->data[$key];
        }
        else
        {
            return FALSE;
        }
    }
	
    /**
     * Get user session id.
     * Can be used for log out form.
     * @return string Session id
     */
	public function session_id()
	{
		return $this->_user->session_id;
	}

    /**
     * Returns user status.
     * @return boolean TRUE is user is logged in, FALSE otherwise.
     */
    public function is_logged_in()
    {
        return $this->_user->data['is_registered'];
    }

    /**
     * Checks if the currently logged-in user is an administrator.
     * @return boolean TRUE if the currently logged-in user is an administrator, FALSE otherwise.
     */
    public function is_administrator()
    {
        return $this->is_group_member('administrators');
    }

    /**
     * Checks if the currently logged-in user is a moderator.
     * @return boolean TRUE if the currently logged-in user is a moderator, FALSE otherwise.
     */
    public function is_moderator()
    {
        return  $this->is_group_member('moderators');
    }

    /**
     * Checks if a user is a member of the given user group.
     * @param  string  $group Group name in lowercase.
     * @return boolean        TRUE if user is a group member, FALSE otherwise.
     */
    public function is_group_member($group)
    {
        $groups = array_map(strtolower, $this->get_user_group_membership());

        if (in_array($group, $groups))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Returns information for a given user.
     * @param  int   $user_id User ID
     * @return mixed          Array with user information on success, FALSE otherwise.
     */
    public function get_user_by_id($user_id)
    {
        return $this->_phpbb->get_user_by_id($user_id);
    }

    /**
     * Returns information for a given user.
     * @param  string $username Username
     * @return mixed            Array with user information on success, FALSE otherwise.
     */
    public function get_user_by_username($username)
    {
        return $this->_phpbb->get_user_by_username($username);
    }

    /**
     * Returns all user groups.
     * @return array User groups
     */
    public function get_user_group_membership()
    {
        return $this->_phpbb->get_user_group_membership($this->_user->data['user_id']);
    }

    /**
     * Send user a private message.
     * @param  int     $sender_id        The sender's user ID
     * @param  string  $sender_ip        The sender's IP address
     * @param  string  $sender_username  The sender's username
     * @param  int     $recipient_id     Recipient ID
     * @param  string  $subject          Message subject
     * @param  string  $message          Message body
     * @param  boolean $enable_signature Attach user signature?
     * @param  boolean $enable_bbcode    Enable BB code?
     * @param  boolean $enable_smilies   Enable smiles?
     * @param  boolean $enable_urls      Enable URLs (automatically wrap URLs in <a> tag)?
     */
    public function send_private_message($sender_id, $sender_ip = '127.0.0.1', $sender_username, $recipient_id, $subject, $message, $enable_signature = FALSE, $enable_bbcode = TRUE, $enable_smilies = TRUE, $enable_urls = TRUE)
    {
        $uid = $bitfield = $options = '';

		generate_text_for_storage($message, $uid, $bitfield, $options, $enable_bbcode, $enable_urls, $enable_smilies);

		$data = array(
            'from_user_id'    => $sender_id,
            'from_user_ip'    => $sender_ip,
            'from_username'   => $sender_username,
            'enable_sig'      => $enable_signature,
            'enable_bbcode'   => $enable_bbcode,
            'enable_smilies'  => $enable_smilies,
            'enable_urls'     => $enable_urls,
            'icon_id'         => 0,
            'bbcode_bitfield' => $bitfield,
            'bbcode_uid'      => $uid,
            'message'         => $message,
            'address_list'    => array('u' => array($recipient_id => 'to'))
		);

		submit_pm('post', $subject, $data, FALSE);
    }

    /**
     * Add user to group.
     * @param  int     $user_id  User ID
     * @param  int     $group_id The user group ID to add user to.
     * @param  boolean $default  If TRUE, will set this group as the default group for the user being added.
     * @param  boolean $leader   If TRUE, user will be a leader of the group.
     * @param  boolean $pending  If TRUE, user needs to be approved before being shown in the group member list.
     * @return mixed             FALSE on success, language string for the relevant error otherwise.
     */
    public function add_user_to_group($user_id, $group_id, $default = FALSE, $leader = FALSE, $pending = FALSE)
    {
        return group_user_add($group_id, $user_id, FALSE, FALSE, $default, $leader, $pending);
    }

    /**
     * Create a new topic (topic will be posted with the currently logged-in user as an author).
     * @param  int     $forum_id         The forum ID
     * @param  string  $subject          Topic subject
     * @param  string  $message          Topic body
     * @param  boolean $enable_signature Attach user signature?
     * @param  boolean $enable_bbcode    Enable BB code?
     * @param  boolean $enable_smilies   Enable smiles?
     * @param  boolean $enable_urls      Enable URLs (automatically wrap URLs in <a> tag)?
     * @return string                    Topic URL on success, forum URL otherwise.
     */
    public function create_new_topic($forum_id, $subject, $message, $enable_signature = FALSE, $enable_bbcode = TRUE, $enable_smilies = TRUE, $enable_urls = TRUE)
    {
        $poll = $uid = $bitfield = $options = '';

        generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
        generate_text_for_storage($message, $uid, $bitfield, $options, $enable_bbcode, $enable_urls, $enable_smilies);

        $data = array(
            'forum_id'          => $forumId,
            'icon_id'           => FALSE,
            
            'enable_bbcode'     => $enableBBcode,
            'enable_smilies'    => $enableSmilies,
            'enable_urls'       => $enableUrls,
            'enable_sig'        => $enableSignature,
            
            'message'           => $message,
            'message_md5'       => md5($message),
            
            'bbcode_bitfield'   => $bitfield,
            'bbcode_uid'        => $uid,
            
            'post_edit_locked'  => 0,
            'topic_title'       => $subject,
            'notify_set'        => FALSE,
            'notify'            => FALSE,
            'post_time'         => 0,
            'forum_name'        => '',
            'enable_indexing'   => TRUE
        );

        return submit_post('post', $subject, '', POST_NORMAL, $poll, $data);
    }
	
}