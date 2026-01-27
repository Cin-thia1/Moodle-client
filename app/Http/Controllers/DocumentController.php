<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Course;
use App\Services\MoodleDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected MoodleDocumentService $documentService;

    public function __construct(MoodleDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Affiche le formulaire de téléchargement
     */
    public function create(Course $course)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('documents.create', compact('course'));
    }

    /**
     * Sauvegarde un nouveau document
     */
    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'filename' => 'required|string|max:255', // Nom d'affichage du document
            'file' => 'required|file|max:10240', // 10MB max, comme dans l'API
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        // Stocker le fichier avec un nom unique pour éviter les conflits.
        $path = $file->storeAs(
            'courses/documents',
            time() . '_' . $originalFilename,
            'public'
        );

        $document = $this->documentService->createDocument(
            $course->id,
            auth()->id(),
            $validated['filename'],
            [
                'filepath' => $path, // Utiliser le chemin du fichier stocké
                'mimetype' => $file->getMimeType(),
                'filesize' => $file->getSize(),
                'file_url' => asset('storage/' . $path) // Optionnel: stocker l'URL d'accès direct
            ]
        );

        return redirect()->route('courses.show', $course)->withFragment('documents')->with('success', 'Document téléchargé avec succès');
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(Course $course, Document $document)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('documents.edit', compact('course', 'document'));
    }

    /**
     * Met à jour un document
     */
    public function update(Request $request, Course $course, Document $document)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'filepath' => 'nullable|string',
        ]);

        $this->documentService->updateDocument($document->id, $validated);

        return redirect()->route('courses.show', $course)->withFragment('documents')->with('success', 'Document mis à jour avec succès');
    }

    /**
     * Supprime un document
     */
    public function destroy(Course $course, Document $document)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $this->documentService->deleteDocument($document->id);

        return redirect()->route('courses.show', $course)->withFragment('documents')->with('success', 'Document supprimé avec succès');
    }

    /**
     * Synchronise les documents depuis Moodle
     */
    public function sync(Course $course)
    {
        if (!auth()->user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->documentService->syncCourseDocuments($course->id);

        return redirect()->route('courses.show', $course)->withFragment('documents')
            ->with('success', "Synchronisation complétée: {$result['synced']} documents synchronisés");
    }

    /**
     * Télécharge un document
     */
    public function download(Course $course, Document $document)
    {
        $this->authorize('view_documents');

        // Si le fichier est stocké localement (chemin valide)
        if ($document->filepath && $document->filepath !== '/' && Storage::disk('public')->exists($document->filepath)) {
            return Storage::disk('public')->download($document->filepath, $document->filename);
        }

        // Si c'est un URL externe (Moodle)
        if ($document->file_url) {
            return redirect($document->file_url);
        }

        return redirect()->back()->with('error', 'Fichier non disponible');
    }

    /**
     * Affiche l'aperçu d'un document
     */
    public function preview(Document $document)
    {
        $this->authorize('view_documents');

        // Si le fichier est stocké localement
        if ($document->filepath && Storage::disk('public')->exists($document->filepath)) {
            $path = Storage::disk('public')->path($document->filepath);
            return response()->file($path);
        }

        // Si c'est un URL externe (Moodle)
        if ($document->file_url) {
            return redirect($document->file_url);
        }

        return redirect()->back()->with('error', 'Fichier non disponible');
    }
}
