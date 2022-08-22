<?php

// use app\controller\UserController;

// #[Attribute(Attribute::TARGET_CLASS)]
// class Controller
// {
//   public function __construct(public string $name)
//   {
//   }
// }

// #[Attribute(Attribute::TARGET_METHOD)]
// class RequestMap
// {
//   public function __construct(public string $path, public string $desc, public array $method)
//   {
//   }
// }


// #[Controller(name: UserController::class)]
// class TestController
// {

//   #[RequestMap(path: '/admin/test', method: ['GET', 'POST'])]
//   public function index()
//   {
//     echo "TestController::index method called\n";
//   }
// }

// $controller = new ReflectionClass(TestController::class);
// $attrs = $controller->getAttributes();
// foreach ($attrs as $a) {
//   var_dump($a->getName());
//   var_dump($a->getArguments());
// }

// $methods = $controller->getMethods();
// foreach ($methods as $method) {
//   $attrs = $method->getAttributes();
//   var_dump($method->getName());
//   call_user_func(array($controller->newInstance(), $method->getName()), null);

//   foreach ($attrs as $a) {
//     var_dump($a->getName());
//     var_dump($a->getArguments());
//   }
// }
