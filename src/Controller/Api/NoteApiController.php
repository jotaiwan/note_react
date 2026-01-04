<?php

// src/Controller/Api/NoteApiController.php
namespace  NoteReact\Controller\Api;

use NoteReact\Factory\NoteFactory;
use NoteReact\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class NoteApiController extends AbstractController
{
    use LoggerTrait;

    private NoteFactory $noteFactory;

    public function __construct(NoteFactory $noteFactory, LoggerInterface $logger)
    {
        $this->noteFactory = $noteFactory;
        $this->setLogger($logger);
    }

    // -----------------------------
    // List all notes
    // -----------------------------
    #[Route('/api/notes', methods: ['GET'], name: 'api_note_list')]
    public function listNotes(): JsonResponse
    {
        // ✅ 手动触发 Xdebug 断点
        // \xdebug_break();
        $data = [];
        $noteService = $this->noteFactory->createNoteService('read');
        $noteRequest = $this->noteFactory->createRequestStrategy('read', $data);

        if (!$noteRequest) {
            return $this->json(['error' => 'Invalid request'], 400);
        }

        $result = $noteService->execute($noteRequest);

        return $this->json($result);
    }

    // -----------------------------
    // Read one note by ID
    // -----------------------------
    #[Route('/api/notes/{id}', methods: ['GET'], name: 'api_note_read', requirements: ['id' => '\d+'])]
    public function readNote(int $id): JsonResponse
    {
        // \xdebug_break();

        $data = ['rowId' => $id];
        $noteService = $this->noteFactory->createNoteService('read');
        $noteRequest = $this->noteFactory->createRequestStrategy('read', $data);

        if (!$noteRequest) {
            return $this->json(['error' => 'Invalid request'], 400);
        }

        $result = $noteService->execute($noteRequest);

        return $this->json($result ?: ['error' => 'Not found'], $result ? 200 : 404);
    }

    // -----------------------------
    // Create a new note
    // -----------------------------
    #[Route('/api/notes', methods: ['POST'], name: 'api_note_create')]
    public function createNote(Request $request): JsonResponse
    {
        \xdebug_break();
        $data = json_decode($request->getContent(), true) ?? [];
        $this->info("******************** Try to save new note: " . json_encode($data));

        $noteService = $this->noteFactory->createNoteService('save');
        $noteRequest = $this->noteFactory->createRequestStrategy('save', $data);

        if (!$noteRequest) {
            return $this->json(['error' => 'Invalid request'], 400);
        }


        try {
            // 原来的保存逻辑
            $result = $noteService->execute($noteRequest);
            $this->info(".................................. " . json_encode($result));
            if ($result["success"]) {
                return $this->json([
                    "success" => true,
                    "savedTicket" => $result["savedTicket"],
                ]);
            } else {
                return $this->json([
                    "success" => false,
                    "savedTicket" => null,
                ], 500); // Optional: return HTTP 500 if insert failed
            }
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    // -----------------------------
    // Update existing note
    // -----------------------------
    #[Route('/api/notes/{id}', methods: ['PUT'], name: 'api_note_update', requirements: ['id' => '\d+'])]
    public function updateNote(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $data['rowId'] = $id;

        $noteService = $this->noteFactory->createNoteService('update');
        $noteRequest = $this->noteFactory->createRequestStrategy('update', $data);

        if (!$noteRequest) {
            return $this->json(['error' => 'Invalid request'], 400);
        }

        $result = $noteService->execute($noteRequest);

        return $this->json($result ?: ['error' => 'Update failed'], $result ? 200 : 400);
    }


    // -----------------------------
    // List of Note Statuses
    // -----------------------------
    #[Route('/api/notes/statuses', methods: ['GET'], name: 'api_list_of_note_status')]
    public function statusList(): JsonResponse
    {
        $this->info("Loading note statuses...");
        return $this->json(array(
            "All" => array("url" => "?statusOnly=", "icon" => "fa-list fa-fw", "color" => "blue", "text" => "All"),
            "Epic" => array("url" => "?statusOnly=epic", "icon" => "fa-bolt fa-fw", "color" => "purple", "text" => "Epic"),
            "Open" => array("url" => "?statusOnly=open", "icon" => "fa-hourglass-half fa-fw", "color" => "blue", "text" => "Open"),
            "Processing" => array("url" => "?statusOnly=processing", "icon" => "fa-spinner fa-spin fa-fw", "color" => "darkcyan", "text" => "Processing"),
            "Follow" => array("url" => "?statusOnly=follow", "icon" => "fa-eye fa-fw", "color" => "indigo", "text" => "Follow"),
            "Resolved" => array("url" => "?statusOnly=resolved", "icon" => "fa-check-circle fa-fw", "color" => "green", "text" => "Resolved"),
            "Unresolved" => array("url" => "?statusOnly=unresolved", "icon" => "fa-minus-circle fa-fw", "color" => "red", "text" => "Unresolved"),
            "Meeting" => array("url" => "?statusOnly=meeting", "icon" => "fa-users fa-fw", "color" => "Salmon", "text" => "Meeting"),
            "NoteOnly" => array("url" => "?statusOnly=noteonly", "icon" => "fa-sticky-note fa-fw", "color" => "grey", "text" => "NoteOnly")
        ));
    }
}
