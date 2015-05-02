<?php
	require 'incl/config_with_app.php';
	
	$di->set('FormController', function () use ($di) {
		$controller = new \Anax\HTMLForm\FormController();
		$controller->setDI($di);
		
		return $controller;
	});
	$di->set('LoginController', function() use ($di) {
		$controller = new \Anax\Login\LoginController();
		$controller->setDI($di);
		
		return $controller;
	});
	$di->set('QuestionController', function () use ($di) {
		$controller = new \Anax\Question\QuestionController();
		$controller->setDI($di);
		
		return $controller;
	});
	$di->set('TagController', function () use ($di) {
		$controller = new \Anax\Tag\TagController();
		$controller->setDI($di);
		
		return $controller;
	});
	
	$di->setShared('db', function() {
		$db = new \Mos\Database\CDatabaseBasic();
		$db->setOptions(require ANAX_APP_PATH . 'config/database_sqlite.php');
		$db->connect();
		
		return $db;
	});
	
	$navbar = "navbar_wgtotw";
	
	if(isset($_SESSION["user"]))
		$navbar = $navbar . "_user";
	
	$app->navbar->configure(ANAX_APP_PATH . 'config/' . $navbar . '.php');
	$app->theme->configure(ANAX_APP_PATH . 'config/theme_wgtotw.php');
	$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
	
	$app->router->add('', function() use ($app) {
		$app->theme->setTitle("Hem");
		
		$app->dispatcher->forward([
			'action' => 'latestQuestions',
			'controller' => 'question'
		]);
		
		$app->dispatcher->forward([
			'action' => 'totalQuestions',
			'controller' => 'tag'
		]);
		
		$app->dispatcher->forward([
			'action' => 'totalQuestions',
			'controller' => 'login'
		]);
	});
	$app->router->add('about', function() use ($app) {
		$app->theme->setTitle("Om");
		
		$main = $app->fileContent->get('wgtotw/about.md');
		$main = $app->textFilter->doFilter($main, 'shortcode, markdown');
		
		$app->views->addString($main, 'main');
	});
	$app->router->add('login', function() use ($app) {
		$app->theme->setTitle("Logga in");
		
		$app->dispatcher->forward([
			'action'     => 'form',
			'controller' => 'login'
		]);
	});
	$app->router->add('logout', function() use ($app) {
		$app->theme->setTitle("Logga ut");
		
		$app->dispatcher->forward([
			'action'     => 'logout',
			'controller' => 'login'
		]);
	});
	$app->router->add('profile', function() use ($app) {
		$app->dispatcher->forward([
			'action' => 'profile',
			'controller' => 'login'
		]);
	});
	$app->router->add('questions', function() use ($app) {
		$app->dispatcher->forward([
			'action' => 'questions',
			'controller' => 'question'
		]);
	});
	$app->router->add('setupLogin', function() use ($app) {
		$app->theme->setTitle("Setup login");
		
		$app->db->dropTableIfExists('user')->execute();
		
		$app->db->createTable(
			'user',
			[
				'created' => ['datetime'],
				'mail' => ['varchar(80)'],
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'password' => ['varchar(255)'],
				'updated' => ['datetime'],
				'username' => ['varchar(20)', 'unique', 'not null']
			]
		)->execute();

		$app->db->insert('user', ['created', 'mail', 'password', 'username']);

		$now = gmdate('Y-m-d H:i:s');

		$app->db->execute([$now, 'admin@dbwebb.se', 'admin', 'admin']);
		$app->db->execute([$now, 'doe@dbwebb.se', 'doe', 'doe']);
		
		$app->redirectTo("");
	});
	$app->router->add('setupQuestions', function() use ($app) {
		$app->theme->setTitle("Setup questions");
		
		$app->db->dropTableIfExists('answer')->execute();
		
		$app->db->createTable(
			'answer',
			[
				'content' => ['text'],
				'created' => ['datetime'],
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'question_id' => ['integer', 'not null'],
				'user_id' => ['integer', 'not null']
			]
		)->execute();
		
		$app->db->dropTableIfExists('answerComment')->execute();
		
		$app->db->createTable(
			'answerComment',
			[
				'answer_id' => ['integer', 'not null'],
				'content' => ['text'],
				'created' => ['datetime'],
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'user_id' => ['integer', 'not null']
			]
		)->execute();
		
		$app->db->dropTableIfExists('question')->execute();
		
		$app->db->createTable(
			'question',
			[
				'content' => ['text'],
				'created' => ['datetime'],
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'subject' => ['varchar(80)'],
				'user_id' => ['integer', 'not null']
			]
		)->execute();
		
		$app->db->dropTableIfExists('questionComment')->execute();
		
		$app->db->createTable(
			'questionComment',
			[
				'content' => ['text'],
				'created' => ['datetime'],
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'question_id' => ['integer', 'not null'],
				'user_id' => ['integer', 'not null']
			]
		)->execute();
		
		$app->db->dropTableIfExists('tag')->execute();
		
		$app->db->createTable(
			'tag',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'name' => ['varchar(80)'],
				'question_id' => ['integer', 'not null']
			]
		)->execute();
		
		$app->redirectTo("");
	});
	$app->router->add('tags', function() use ($app) {
		$app->dispatcher->forward([
			'action' => 'tags',
			'controller' => 'tag'
		]);
	});
	$app->router->add('users', function() use ($app) {
		$app->dispatcher->forward([
			'action' => 'users',
			'controller' => 'login'
		]);
	});

	$app->router->handle();
	$app->theme->render();
?>