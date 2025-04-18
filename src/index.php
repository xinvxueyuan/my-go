<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

$app = AppFactory::create();

// 添加错误处理中间件
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

// 添加路由中间件
$app->addRoutingMiddleware();

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
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response->withStatus(400)->getBody()->write(json_encode(['error' => '请填写作者姓名']));
    }
    
    // 生成许可证文件
    $generator = new \App\LicenseGenerator($data);
    $filename = $generator->generate();
    
    // 返回文件URL
    $fileUrl = '/html/' . $filename;
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode([
        'success' => true,
        'file_url' => $fileUrl
    ]));
    return $response;
});

// 添加静态文件访问路由
$app->get('/html/{filename}', function ($request, $response, $args) {
    $filename = $args['filename'];
    $filepath = __DIR__ . '/../public/html/' . $filename;
    
    if (!file_exists($filepath)) {
        return $response->withStatus(404)->getBody()->write('File not found');
    }
    
    $response = $response->withHeader('Content-Type', 'text/html');
    $response->getBody()->write(file_get_contents($filepath));
    return $response;
});

$app->run();