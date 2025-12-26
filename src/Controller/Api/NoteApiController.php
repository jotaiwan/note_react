<?php

// src/Controller/Api/NoteApiController.php
namespace Note\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Note\Factory\NoteFactory;

use Psr\Log\LoggerInterface;  // 需要导入这个接口
use Note\Util\LoggerTrait;

class NoteApiController extends AbstractController
{

    use LoggerTrait;

    private NoteFactory $noteFactory;

    public function __construct(NoteFactory $noteFactory, LoggerInterface $logger)
    {
        $this->noteFactory = $noteFactory;
        $this->setLogger($logger);
    }

    /**
     * Note Only: 
     * ?int $rowId
     * - Use ?int to indicate that id is an optional parameter. This means id can be null and is no longer a required int. 
     *   If id is not provided, Symfony will set it to null.
     */
    #[Route("/api/note/{action}/{rowId}", name: "api_{action}_note_by_{id}", methods: ["GET", "POST"])]
    #[Route("/api/note/{action}", name: "api_note_{action}", methods: ["GET", "POST"])]
    public function apiAction(string $action, ?int $rowId, Request $request): JsonResponse
    {
        try {
            if ($rowId !== null) {
                $this->info("Request to `{$action}` the note by id `{$rowId}`: " . $request->getContent());
            } else {
                $this->info("Request to `{$action}` the note: " . $request->getContent());
            }

            // Use a Factory to create the corresponding operation’s NoteService. 
            $noteService = $this->noteFactory->createNoteService($action);

            // Data can be retrieved from a POST request, but from a GET request it returns null.
            $data = $request->isMethod('POST') ? json_decode($request->getContent(), true) : [];
            // add rowId into $data, it could be null
            $data["rowId"] = $rowId;

            // Special case: for 'read' action we treat the route parameter as 'days' (matches UI route semantics)
            if ($action === 'read') {
                $data['days'] = $rowId;
            }

            $this->info("Request action `{$action}` with type `" . gettype($data) . "`: " . json_encode($data));

            $noteRequest = $this->noteFactory->createRequestStrategy($action, $data);

            $result = $noteService->execute($noteRequest);
            if ($result === null) {
                $statusCode = match ($action) {
                    'read'   => 404,
                    'update' => 404,
                    default  => 400, // save / others
                };

                return new JsonResponse(
                    ['error' => "Request `$action` failed"],
                    $statusCode
                );
            }

            // response JSON format
            return new JsonResponse($result);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
