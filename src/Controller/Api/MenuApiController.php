<?php

namespace NoteReact\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
// use NoteReact\Service\EmojiService;
use NoteReact\Service\MenuService;

use NoteReact\CredentialReader\CredentialReader;
use NoteReact\Util\LoggerTrait;
use Psr\Log\LoggerInterface;

class MenuApiController extends AbstractController
{

    use LoggerTrait;

    // private $emojiService;
    private $menuService;

    public function __construct(MenuService $menuService, LoggerInterface $logger)
    {
        $this->menuService = $menuService;
        // $this->emojiService = $emojiService;
        $this->setLogger($logger);
    }

    #[Route('/api/emojis', name: 'get_emojis', methods: ['GET'])]
    public function listEmojis()
    {
        try {
            // Call the service to get emojis
            $emojis = $this->menuService->getAllEmojis();
            $this->info("Total " . count($emojis) . " are loaded.");
            return new JsonResponse($emojis);
        } catch (\Exception $e) {
            // Catch any exception and return an error message
            return new JsonResponse(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/clipboard', name: 'get list of clipboard', methods: ['GET'])]
    public function listClipboard()
    {
        try {
            // Call the service to get emojis
            $clipboard = $this->menuService->getClipboard();
            $this->info("Total " . count($clipboard) . " are loaded.");
            return new JsonResponse($clipboard);
        } catch (\Exception $e) {
            // Catch any exception and return an error message
            return new JsonResponse(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/credential/{resource}/{key}', name: 'get credential source key value', methods: ['GET'])]
    public function getResouceKeyValue(string $resource, string $key)
    {
        \xdebug_break();
        return new JsonResponse(CredentialReader::getCredential($resource, $key));
    }

    #[Route('/api/credential', name: 'get accoutn credential', methods: ['GET'])]
    public function accountCredential()
    {
        return new JsonResponse(CredentialReader::getTaSso());
    }

    #[Route('/api/links/projects', methods: ['GET'])]
    public function projectLinks(): JsonResponse
    {
        $menuData = $this->menuService->buildAllProjectLinks();

        // \xdebug_break();
        return $this->json($menuData);
    }
}
