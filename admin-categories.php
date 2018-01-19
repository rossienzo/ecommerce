<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

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

 ?>