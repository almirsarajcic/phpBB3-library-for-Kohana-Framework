phpBB3-library-for-Kohana-Framework
===================================
This is a rewritten library from CodeIgniter. 
Original author of CodeIgniter phpBB library is Tomaž Muraus (<a href="http://www.tomaz-muraus.info">http://www.tomaz-muraus.info</a>). It can be found at <a href="http://github.com/EllisLab/CodeIgniter/wiki/phpBB3-library">http://github.com/EllisLab/CodeIgniter/wiki/phpBB3-library</a>.

Tested using Kohana 3.2.

Installation instructions:
<ol>
	<li>Put the folder phpbb in modules folder.</li>
	<li>Enable module in bootstrap.php ('phpbb' => MODPATH.'phpbb')</li>
	<li>Change phpbb\config\phpbb.php (forum path and database config group name - if it's not in other database than your site and doesn't have different prefix type 'default')</li>
	<li>Rename the following files, folders and class names from Session to MySiteSession (or something else):
		<ul>
			<li>/system/classes/session.php</li>
			<li>/system/classes/session/cookie.php</li>
			<li>/system/classes/session/exception.php</li>
			<li>/system/classes/session/native.php</li>
			<li>/system/classes/kohana/session.php</li>
			<li>/system/classes/kohana/session/cookie.php</li>
			<li>/system/classes/kohana/session/exception.php</li>
			<li>/system/classes/kohana/session/native.php</li>
		</ul>
	</li>
</ol>

<strong>NOTE</strong>: After each update of Kohana core files you will need to repeat step 4.<br /> I had to add 4th step because there are some classes with the same name in phpbb and Kohana.<br />
In CodeIgniter classes that are not part of CI are called by $this->CI->class->function() but in Kohana there's no such thing and that is causing errors.<br />
After doing that you can still use Session class by calling MySiteSession::instance().<br />
I've found this solution here: <a href="http://stackoverflow.com/questions/8788298/kohana-3-2-phpbb-library-working-with-abstract-methods">http://stackoverflow.com/questions/8788298/kohana-3-2-phpbb-library-working-with-abstract-methods</a>