phpBB3-library-for-Kohana-Framework
===================================
This is a rewritten library from CodeIgniter. <br />
Original author of CodeIgniter phpBB library is Tomaž Muraus (http://www.tomaz-muraus.info). <br />
It can be found at http://codeigniter.com/wiki/phpBB3_library

<p>Tested using Kohana 3.2.</p>

<p>Installation instructions:
1. Put the folder phpbb in modules folder.
2. Enable module in bootstrap.php ('phpbb' => MODPATH.'phpbb')
3. Change phpbb\config\phpbb.php (forum path and database config group name - if it's not in other database than your site and doesn't have different prefix type 'default')
4. Rename the following files, folders and class names from Session to MySiteSession (or something else):
	/system/classes/session.php
	/system/classes/session/cookie.php
	/system/classes/session/exception.php
	/system/classes/session/native.php
	/system/classes/kohana/session.php
	/system/classes/kohana/session/cookie.php
	/system/classes/kohana/session/exception.php
	/system/classes/kohana/session/native.php
</p>

<p>NOTE: After each update of Kohana core files you will need to repeat step 4. I had to add 4th step because there are some classes with the same name in phpbb and Kohana. In CodeIgniter classes that are not part of CI are called by $this->CI->class->function() but in Kohana there's no such thing and that is causing errors. After doing that you can still use Session class by calling MySiteSession::instance(). I've found this solution here: <a href="http://stackoverflow.com/questions/8788298/kohana-3-2-phpbb-library-working-with-abstract-methods">http://stackoverflow.com/questions/8788298/kohana-3-2-phpbb-library-working-with-abstract-methods</a></p>