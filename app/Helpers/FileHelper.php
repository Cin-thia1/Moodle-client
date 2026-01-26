<?php

namespace App\Helpers;

class FileHelper
{
    /**
     * Formate un nombre de bytes en taille lisible (KB, MB, GB)
     * 
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Récupère l'extension d'un fichier
     * 
     * @param string $filename
     * @return string
     */
    public static function getExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Récupère l'icône Font Awesome pour un type de fichier
     * 
     * @param string $filename
     * @return string
     */
    public static function getFileIcon($filename)
    {
        $extension = self::getExtension($filename);
        
        $icons = [
            'pdf' => 'fas fa-file-pdf text-red-500',
            'doc' => 'fas fa-file-word text-blue-500',
            'docx' => 'fas fa-file-word text-blue-500',
            'xls' => 'fas fa-file-excel text-green-500',
            'xlsx' => 'fas fa-file-excel text-green-500',
            'ppt' => 'fas fa-file-powerpoint text-orange-500',
            'pptx' => 'fas fa-file-powerpoint text-orange-500',
            'zip' => 'fas fa-file-archive text-purple-500',
            'rar' => 'fas fa-file-archive text-purple-500',
            'txt' => 'fas fa-file-text text-gray-500',
            'jpg' => 'fas fa-file-image text-pink-500',
            'jpeg' => 'fas fa-file-image text-pink-500',
            'png' => 'fas fa-file-image text-pink-500',
            'gif' => 'fas fa-file-image text-pink-500',
            'mp3' => 'fas fa-file-audio text-indigo-500',
            'mp4' => 'fas fa-file-video text-red-600',
        ];
        
        return $icons[$extension] ?? 'fas fa-file text-gray-500';
    }

    /**
     * Vérifie si un fichier est un image
     * 
     * @param string $filename
     * @return bool
     */
    public static function isImage($filename)
    {
        $extension = self::getExtension($filename);
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    /**
     * Vérifie si un fichier est un PDF
     * 
     * @param string $filename
     * @return bool
     */
    public static function isPdf($filename)
    {
        return self::getExtension($filename) === 'pdf';
    }
}
