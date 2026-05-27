<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Retourne l'URL d'une image de cours.
     * 
     * @param string|null $imagePath Le chemin de l'image stockée en BD
     * @return string L'URL complète ou un placeholder
     */
    public static function getCourseImageUrl(?string $imagePath): string
    {
        // Si pas d'image, retourner le placeholder
        if (!$imagePath) {
            return asset('images/mathematics.jpeg');
        }

        // Vérifier que le fichier existe dans le disque public
        if (Storage::disk('public')->exists($imagePath)) {
            // Retourner l'URL via le symlink public/storage
            return asset('storage/' . $imagePath);
        }

        // Si le fichier n'existe pas, retourner le placeholder
        return asset('images/mathematics.jpeg');
    }

    /**
     * Vérifie si une image existe.
     */
    public static function courseImageExists(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        return Storage::disk('public')->exists($imagePath);
    }
}
