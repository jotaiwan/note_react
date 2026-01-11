<?php

namespace  NoteReact\Factory;

use Psr\Log\LoggerInterface;
use NoteReact\Util\LoggerTrait;

use NoteReact\Contract\NoteServiceInterface;
use NoteReact\Service\ReadFileService;
use NoteReact\Service\SaveFileService;
use NoteReact\Service\UpdateFileService;

use NoteReact\Repository\ReadFileRepository;
use NoteReact\Repository\SaveFileRepository;
use NoteReact\Repository\UpdateFileRepository;

use NoteReact\Service\HtmlHeadService;
use NoteReact\Service\MenuService;
use NoteReact\Service\NoteBuilderService;

use NoteReact\Strategy\ReadRequestStrategy;
use NoteReact\Strategy\SaveRequestStrategy;
use NoteReact\Strategy\UpdateRequestStrategy;
use Symfony\Component\CssSelector\Parser\Reader;

class NoteFactory
{
    use LoggerTrait;

    private $logger;
    private MenuService $menuService;

    public function __construct(LoggerInterface $logger, MenuService $menuService)
    {
        $this->logger = $logger;
        $this->menuService = $menuService;
    }

    public function createNoteService(string $action): NoteServiceInterface
    {
        $readFileRepository = new ReadFileRepository($this->logger);
        $saveFileRepository = new SaveFileRepository($readFileRepository, $this->logger);
        $updateFileRepository = new UpdateFileRepository($readFileRepository, $this->logger);
        $htmlHeadService = new HtmlHeadService();
        $menuService = $this->menuService;
        $noteBuilderService = new NoteBuilderService();

        switch ($action) {
            case 'read':
                $this->info("Creating `ReadFileService` instance.");
                $readService = new ReadFileService(
                    $readFileRepository,
                    $htmlHeadService,
                    $menuService,
                    $noteBuilderService
                );
                $readService->setLogger($this->logger);
                return $readService;
            case 'save':
                $this->info("Creating `SaveFileService` instance.");
                return new SaveFileService($saveFileRepository, $readFileRepository, $this->logger);
            case 'update':
                $this->info("Creating `UpdateFileService` instance.");
                return new UpdateFileService($updateFileRepository, $readFileRepository, $noteBuilderService, $this->logger);
            default:
                $this->error("Invalid action provided to NoteFactory: {$action}");
                throw new \Exception('Invalid action');
        }
    }


    // 创建 RequestStrategy
    /**
     * What is RequestStrategy?
     * RequestStrategy is an interface (or abstract class) whose main purpose is to provide a standardized way to handle data for different types of requests. 
     * Its functionality and usage usually involve encapsulating the specific logic for processing a request,
     * such as extracting parameters from the request, performing specific validations, or transforming data. 
     * Through the strategy pattern, RequestStrategy allows you to create different request handling strategies for different operations (e.g., save, update, read),
     * maintaining flexibility and extensibility in your code.
     *
     * In your example, RequestStrategy is used to encapsulate the processing logic required for each type of request (save, update, read), 
     * allowing the same NoteService to handle different types of requests.
     *
     * Core features:
     * 1. Unified interface: All request strategy implementation classes (e.g., SaveRequestStrategy, UpdateRequestStrategy, etc.) must implement the RequestStrategy interface.
     *    This ensures that they all have a standardized handle method, which is used to process the specific request.
     *
     * 2. Decouples controller and business logic: RequestStrategy extracts the request handling logic from the controller, making the controller simpler.
     *    The controller only needs to create the corresponding request strategy based on the action
     *    and pass it to the service layer, while the business logic layer handles processing the request and performing operations.
     *
     * 3. Handles request data: RequestStrategy is responsible for extracting, processing, and formatting data from HTTP requests
     *    (e.g., extracting query parameters from GET requests, form data from POST requests, etc.).
     *    It can convert the data into the format required by the service layer as needed.
     *
     * 4. Extensibility: Since each operation (save, update, read) can have its own request strategy class,
     *    you can easily extend new types of request handling logic without modifying existing controller or service layer code.
     * 
     * 
     * Chinese 
     * 什么是 RequestStrategy？ *
     * RequestStrategy 是一个 接口（或抽象类），它的主要目的是为不同类型的请求提供一种标准化的方式来处理数据。它的功能和用途通常是封装处理请求的具体逻辑，
     * 例如从请求中提取参数、执行特定的验证或转换操作等。通过 策略模式，RequestStrategy 可以让你为不同的操作（如 save、update、read）创建不同的请求处理策略，
     * 从而保持代码的灵活性和可扩展性。 *
     * 在你的例子中，RequestStrategy 用来封装每种请求（save、update、read）所需要的处理逻辑，使得同样的 NoteService 可以处理不同类型的请求。 *
     * 核心功能
     * 1. 统一接口：所有的请求策略实现类（如 SaveRequestStrategy、UpdateRequestStrategy 等）都必须实现 RequestStrategy 接口。
     *    这确保了它们都有一个标准化的 handle 方法，这个方法用来处理具体的请求。 *
     * 2. 解耦控制器和业务逻辑：RequestStrategy 将请求的处理逻辑从控制器中提取出来，让控制器变得更简洁。控制器只需要根据 action 创建相应的请求策略，
     *    并将其传递给业务服务层，业务逻辑层负责处理请求和执行操作。 *
     * 3. 处理请求数据：RequestStrategy 负责从 HTTP 请求中提取、处理和格式化数据（例如：提取 GET 请求中的查询参数、POST 请求的表单数据等）。
     *    它可以根据需要将这些数据转换成服务层所需要的格式。 *
     * 4. 扩展性：由于每个操作（save、update、read）可以有自己的请求策略类，你可以非常方便地扩展新类型的请求处理逻辑，而不需要修改现有的控制器和服务层代码
     */
    public function createRequestStrategy(string $action, array $data)
    {
        switch ($action) {
            case 'read':
                return new ReadRequestStrategy($data);
            case 'save':
                return new SaveRequestStrategy($data);
            case 'update':
                return new UpdateRequestStrategy($data);
            default:
                throw new \Exception("Unknown action: " . $action);
        }
    }
}
