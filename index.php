<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");
});

$app->get('/admin', function() {
	//Método que verifica se a pessoa está logada
	user::verifyLogin(); 
    
	$page = new PageAdmin();

	$page->setTpl("index");
});

$app->get('/admin/login', function() {
    
	$page = new PageAdmin([
		//Desabilita o header e o footer da page
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});


//Rota para fazer logout
$app->get('/admin/logout', function() {

	User::logout();

	header("Location: /admin/login");
	exit;
});


/**********Rota para listar usuários*************/

$app->get("/admin/users", function(){

	user::verifyLogin(); 

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));

});



/***********Rota para criar usuário*************/

$app->get("/admin/users/create", function(){

	user::verifyLogin(); 

	$page = new PageAdmin();

	$page->setTpl("users-create");

});



/**************Deletar o usuário***************/

$app->get("/admin/users/:iduser/delete", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;
});



/*************rota para editar usuário************/

$app->get("/admin/users/:iduser", function($iduser) {
	//A solicitação do id serve para solicitar as 
	//informações do usuário específico
	user::verifyLogin(); 
	$user = new User();
    $user->get((int)$iduser);//converte para numérico para certificar que seja numerico
	$page = new PageAdmin();

	$page ->setTpl("users-update", array(
        "user"=>$user->getValues()
 
    ));

});


$app->post("/admin/users/create", function(){

	User::verifyLogin();

	$user = new User();

	//verifica se a opção admin está marcada
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; 
	
	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
});



/***********Insert do usuário***************/

$app->post("/admin/users/create", function() {

	User::verifyLogin();

});



/**********faz a inserção no BD*********/

$app->post("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; //verifica se a opção admin está marcada

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});


$app->get("/admin/forgot", function() {

	$page = new PageAdmin([
		//Desabilita o header e o footer da page
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		//Desabilita o header e o footer da page
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");


});


///// CATEGORIES - INÍCIO DA ROTA LIST /////

$app->get("/admin/categories", function(){

	user::verifyLogin(); 

	$categories = Category::listAll();
	
	$page = new PageAdmin();

	$page->setTpl("categories", [
		'categories'=>$categories
	]);

});

///// FIM DA ROTA LIST /////

///// CATEGORIES - INÍCIO DA ROTA CREATE /////

$app->get("/admin/categories/create", function(){

	user::verifyLogin(); 
	
	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create", function(){

	user::verifyLogin(); 

	$category = new Category();
	
	$category->setData($_POST);

	$category->save();

	header('Location: \admin\categories');
	exit;

});

///// FIM DA ROTA CREATE /////


///// CATEGORIES - ROTA DELETE //////

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	user::verifyLogin(); 
	
	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('Location: \admin\categories');
	exit;

});


///// FIM DA ROTA DELETAR /////



////// INÍCIO DA ROTA UPDATE /////
				  

$app->get("/admin/categories/:idcategory", function($idcategory){

	user::verifyLogin(); 
	
	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){

	user::verifyLogin(); 
	
	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header('Location: \admin\categories');
	exit;
});

///// FIM DA ROTA UPDATE /////


///// INÍCIO DA ROTA PARA AS CATEGORIAS/////

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>[]
	]);
});


///// FIM DA ROTA PARA AS CATEGORIAS/////

$app->run();

 ?>