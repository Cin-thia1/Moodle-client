<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Course;
use App\Services\MoodleDocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected MoodleDocumentService $documentService;

    public function __construct(MoodleDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Affiche la liste des documents d'un cours
     */
    public function index(Course $course)
    {
        $this->authorize('view_documents');
        
        $documents = $this->documentService->getCourseDocuments($course->id);
        return view('documents.index', compact('course', 'documents'));
    }

    /**
     * Affiche le formulaire de téléchargement
     */
    public function create(Course $course)
    {
        $this->authorize('upload_document');
        return view('documents.create', compact('course'));
    }

    /**
     * Sauvegarde un nouveau document
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('upload_document');

        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'file' => 'required|file',
            'filepath' => 'nullable|string',
        ]);

        $document = $this->documentService->createDocument(
            $course->id,
            auth()->id(),
            $validated['filename'],
            [
                'filepath' => $validated['filepath'] ?? '/',
                'mimetype' => $request->file('file')->getMimeType(),
                'filesize' => $request->file('file')->getSize(),
            ]
        );

        return redirect()->route('documents.index', $course)->with('success', 'Document téléchargé avec succès');
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(Course $course, Document $document)
    {
        $this->authorize('edit_document');
        return view('documents.edit', compact('course', 'document'));
    }

    /**
     * Met à jour un document
     */
    public function update(Request $request, Course $course, Document $document)
    {
        $this->authorize('edit_document');

        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'filepath' => 'nullable|string',
        ]);

        $this->documentService->updateDocument($document->id, $validated);

        return redirect()->route('documents.index', $course)->with('success', 'Document mis à jour avec succès');
    }

    /**
     * Supprime un document
     */
    public function destroy(Course $course, Document $document)
    {
        $this->authorize('delete_document');

        $this->documentService->deleteDocument($document->id);

        return redirect()->route('documents.index', $course)->with('success', 'Document supprimé avec succès');
    }

    /**
     * Synchronise les documents depuis Moodle
     */
    public function sync(Course $course)
    {
        $this->authorize('upload_document');

        $result = $this->documentService->syncCourseDocuments($course->id);

        return redirect()->route('documents.index', $course)
            ->with('success', "Synchronisation complétée: {$result['synced']} documents synchronisés");
    }

    /**
     * Télécharge un document
     */
    public function download(Course $course, Document $document)
    {
        $this->authorize('view_documents');

        if ($document->file_url) {
            return redirect($document->file_url);
        }

        return redirect()->back()->with('error', 'Fichier non disponible');
    }
}
