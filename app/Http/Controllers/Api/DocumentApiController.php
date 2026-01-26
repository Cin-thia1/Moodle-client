<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentApiController extends Controller
{
    /**
     * Ajoute un document au cours
     */
    public function store(Request $request, Course $course)
    {
        // Vérifier que l'utilisateur est enseignant ou admin
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $validated = $request->validate([
            'document_file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:500'
        ]);

        try {
            // Stocker le fichier
            $file = $request->file('document_file');
            $filename = $file->getClientOriginalName();
            
            // Créer un chemin unique
            $path = Storage::disk('public')->putFileAs(
                'courses/documents',
                $file,
                time() . '_' . $filename
            );

            // Créer l'enregistrement dans la base de données
            $document = Document::create([
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'filename' => $filename,
                'filepath' => $path,
                'mimetype' => $file->getMimeType(),
                'filesize' => $file->getSize(),
                'file_url' => asset('storage/' . $path),
                'status' => 1,
                'file_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès',
                'document' => $document
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un document
     */
    public function destroy(Document $document)
    {
        // Vérifier que l'utilisateur est enseignant ou admin
        if (!Auth::user()->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN'])) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        try {
            // Supprimer le fichier du stockage
            if ($document->filepath && Storage::disk('public')->exists($document->filepath)) {
                Storage::disk('public')->delete($document->filepath);
            }

            // Supprimer l'enregistrement
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharge un document
     */
    public function download(Document $document)
    {
        try {
            // Si le fichier est stocké localement (chemin valide)
            if ($document->filepath && $document->filepath !== '/' && Storage::disk('public')->exists($document->filepath)) {
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche l'aperçu d'un document
     */
    public function preview(Document $document)
    {
        try {
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
