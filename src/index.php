<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

$app = AppFactory::create();

// 添加视图渲染器中间件
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// 设置视图渲染器
$renderer = new PhpRenderer(__DIR__ . '/../templates');

// 路由定义
$app->get('/', function ($request, $response) use ($renderer) {
    return $renderer->render($response, 'form.php');
});

// 处理表单提交
$app->post('/generate', function ($request, $response) {
    $data = $request->getParsedBody();
    
    // 验证表单数据
    if (!isset($data['author_name']) || empty($data['author_name'])) {
        return $response->withStatus(400)->withJson(['error' => '请填写作者姓名']);
    }
    
    // TODO: 实现许可证生成逻辑
    
    return $response->withHeader('Content-Type', 'application/json')
                    ->withJson(['success' => true]);
});

$app->run();