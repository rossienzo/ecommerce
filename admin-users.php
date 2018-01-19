<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;


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


 ?>