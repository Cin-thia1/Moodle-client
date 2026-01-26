<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentApiController extends Controller
{
    /**
     * Upload un nouveau document
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('upload', Document::class);

        $validated = $request->validate([
            'document_file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Stocker le fichier
            $file = $request->file('document_file');
            $filename = $file->getClientOriginalName();
            $storagePath = 'courses/documents/' . $course->id;
            
            // Générer un nom unique
            $uniqueFilename = Str::uuid() . '_' . $filename;
            $path = $file->storeAs($storagePath, $uniqueFilename, 'public');

            // Créer le document en base de données
            $document = Document::create([
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'filename' => $filename,
                'filepath' => $path,
                'mimetype' => $file->getMimeType(),
                'filesize' => $file->getSize(),
                'status' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès',
                'document' => $document,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Télécharge un document
     */
    public function download(Document $document)
    {
        $this->authorize('view', $document);

        // Si le fichier est stocké localement
        if ($document->filepath && Storage::disk('public')->exists($document->filepath)) {
            return Storage::disk('public')->download($document->filepath, $document->filename);
        }

        // Si c'est un URL externe (Moodle)
        if ($document->file_url) {
            return redirect($document->file_url);
        }

        return response()->json([
            'success' => false,
            'message' => 'Fichier non disponible',
        ], 404);
    }

    /**
     * Affiche un aperçu du document
     */
    public function preview(Document $document)
    {
        $this->authorize('view', $document);

        // Si le fichier est stocké localement
        if ($document->filepath && Storage::disk('public')->exists($document->filepath)) {
            $path = Storage::disk('public')->path($document->filepath);
            return response()->file($path);
        }

        // Si c'est un URL externe
        if ($document->file_url) {
            return redirect($document->file_url);
        }

        return response()->json([
            'success' => false,
            'message' => 'Fichier non disponible',
        ], 404);
    }

    /**
     * Supprime un document
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        try {
            // Supprimer le fichier du storage
            if ($document->filepath && Storage::disk('public')->exists($document->filepath)) {
                Storage::disk('public')->delete($document->filepath);
            }

            // Supprimer le document de la base de données
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
            ], 500);
        }
    }
}
