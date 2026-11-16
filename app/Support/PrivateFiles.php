<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Stockage des fichiers clients (documents, livrables, pièces jointes).
 *
 * Les fichiers sont enregistrés sur le disque privé « local » (storage/app/private),
 * qui n'est jamais exposé par le lien public /storage. Ils ne sont servis que par
 * des routes contrôlées par ServiceRequestPolicy.
 */
class PrivateFiles
{
    public const DISK = 'local';

    /** Ancien disque utilisé avant la correction : lu uniquement pour les fichiers déjà existants. */
    public const LEGACY_DISK = 'public';

    public const ALLOWED_MIMES = 'pdf,doc,docx,xls,xlsx,png,jpg,jpeg';

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, self::DISK);
    }

    public static function download(?string $path): StreamedResponse
    {
        abort_unless(is_string($path) && $path !== '' && ! str_contains($path, '..'), 404);

        foreach ([self::DISK, self::LEGACY_DISK] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->download($path);
            }
        }

        abort(404);
    }

    public static function deleteDirectory(string $directory): void
    {
        foreach ([self::DISK, self::LEGACY_DISK] as $disk) {
            Storage::disk($disk)->deleteDirectory($directory);
        }
    }
}
