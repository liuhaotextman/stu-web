<?php

namespace Snow\StuWeb\Http;

use Snow\StuWeb\Container\Container;
use Snow\StuWeb\Contracts\Http\RequestInterface;
use Snow\StuWeb\Contracts\Http\ResponseInterface;
use Snow\StuWeb\Contracts\Middleware\MiddlewareManagerInterface;
use Snow\StuWeb\Contracts\Routing\DispatcherInterface;
use Snow\StuWeb\Contracts\Routing\RouteInterface;
use Snow\StuWeb\Factory\createResponse;
use Snow\StuWeb\Middleware\MiddlewareManager;
use Snow\StuWeb\Routing\Dispatcher;
use Snow\StuWeb\Routing\RouteCollector;

class App extends Container
{
    protected $bind = [
        RouteInterface::class => RouteCollector::class,
        MiddlewareManagerInterface::class => MiddlewareManager::class,
        RequestInterface::class => Request::class,
        DispatcherInterface::class => Dispatcher::class
    ];

    protected RouteInterface $route;

    protected MiddlewareManagerInterface $middleware;

    protected RequestInterface $request;

    protected DispatcherInterface $dispatcher;

    protected string $appPath = '';

    protected string $routePath = '';

    protected string $configPath = '';

    public function __construct(string $appPath = '')
    {
        if (!$appPath) {
            $appPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App';
        }
        $this->appPath = $appPath;
        $this->routePath = $appPath . DIRECTORY_SEPARATOR . 'Route';
        $this->configPath = $appPath . DIRECTORY_SEPARATOR . 'Config';
    }

    /**
     * 初始化组件
     */
    protected function initComponent()
    {
        if (file_exists($this->appPath . DIRECTORY_SEPARATOR . 'provider.php')) {
            $bind = require $this->appPath . DIRECTORY_SEPARATOR . 'provider.php';
            $this->bind = array_merge($this->bind, $bind);
        }
        $this->instance(App::class, $this);
        $this->route = $this->get(RouteInterface::class);
        $this->middleware = $this->get(MiddlewareManagerInterface::class);
        $this->request = $this->get(RequestInterface::class);
        $this->dispatcher = $this->get(DispatcherInterface::class);
    }

    /**
     * 初始化配置
     */
    protected function initConfig()
    {
        //加载路由文件
        $files = glob($this->routePath . DIRECTORY_SEPARATOR . '*.php');
        foreach ($files as $file) {
            if (file_exists($file)) require_once $file;
        }
    }

    /**
     * 初始化应用
     */
    protected function init()
    {
        $this->initComponent();
        $this->initConfig();
    }

    /**
     * 启动应用
     * @return ResponseInterface
     */
    public function run(): ResponseInterface
    {
        $this->init();
        return $this->runWithRequest($this->request);
    }

    /**
     * 处理请求
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function runWithRequest(RequestInterface $request): ResponseInterface
    {
        $routeResult = $this->dispatcher->routeResult($request->getMethod(), $request->getPath());
        $response = $this->middleware->pipelines($routeResult->getMiddlewares())->send($request)->then(function ($request) use ($routeResult) {
            $response =  $this->invoke($routeResult->getCallable(), ['request' => $request]);
            return createResponse::createResponse($response);
        });
        return $response;
    }

    public function getAppPath(): string
    {
        return $this->appPath;
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }
}