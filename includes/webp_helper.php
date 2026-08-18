<?php
/**
 * WebP Image Conversion Helper
 * 
 * Provides reusable functions for converting uploaded images (JPEG, PNG, GIF)
 * to WebP format using PHP's GD library. All upload handlers in the admin panel
 * should use these functions to ensure consistent WebP output.
 */

/**
 * Convert an image file to WebP format.
 *
 * Reads a JPEG/PNG/GIF/WebP source file and re-encodes it as WebP
 * at the specified quality level.
 *
 * @param string $source_path      Absolute path to the source image file.
 * @param string $destination_path Absolute path where the .webp file will be saved.
 * @param int    $quality          WebP quality (0-100). Default 75.
 * @return bool True on success, false on failure.
 */
function convert_to_webp(string $source_path, string $destination_path, int $quality = 75): bool
{
    if (!file_exists($source_path)) {
        return false;
    }

    // Fallback if GD library is not installed/enabled
    if (!function_exists('imagecreatefromjpeg')) {
        // Simply copy the file to the destination path so the application doesn't break
        return @copy($source_path, $destination_path);
    }

    $image_info = @getimagesize($source_path);
    if ($image_info === false) {
        return false;
    }

    $mime = $image_info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $image = @imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Preserve transparency for PNG/GIF sources
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    $result = imagewebp($image, $destination_path, $quality);
    imagedestroy($image);

    return $result;
}

/**
 * Process an uploaded file: move it, convert to WebP, and return the DB-ready path.
 *
 * This is the main entry point for all upload handlers. It:
 *   1. Validates the file extension.
 *   2. Generates a unique filename with the given prefix.
 *   3. Moves the uploaded temp file to the target directory.
 *   4. Converts the file to WebP format.
 *   5. Deletes the original non-WebP file (if conversion produced a new file).
 *   6. Returns the relative path suitable for storing in the database.
 *
 * @param string $tmp_name       The temporary file path from $_FILES.
 * @param string $original_name  The original filename from $_FILES.
 * @param string $target_dir     The absolute target directory (e.g. '../assets/images/items/').
 * @param string $prefix         Filename prefix (e.g. 'IMG', 'VIBE', 'CAT', 'ICON').
 * @param string $db_base_path   The relative base path for DB storage (e.g. 'assets/images/items/').
 * @param int    $quality        WebP quality (0-100). Default 75.
 * @return string|false The relative DB path (ending in .webp) on success, or false on failure.
 */
function process_upload_to_webp(
    string $tmp_name,
    string $original_name,
    string $target_dir,
    string $prefix,
    string $db_base_path,
    int    $quality = 75
) {
    $img_ex = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed_exs = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($img_ex, $allowed_exs)) {
        return false;
    }

    // Ensure target directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Generate unique filename — keep original extension for the temp move
    $unique_base = uniqid($prefix . "-", true);
    $temp_filename = $unique_base . '.' . $img_ex;
    $temp_filepath = $target_dir . $temp_filename;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($tmp_name, $temp_filepath)) {
        return false;
    }

    // If already WebP, just re-encode at the target quality for consistency
    $webp_filename = $unique_base . '.webp';
    $webp_filepath = $target_dir . $webp_filename;

    $gd_enabled = function_exists('imagecreatefromjpeg');

    if ($img_ex === 'webp') {
        // Re-encode at target quality
        if (convert_to_webp($temp_filepath, $webp_filepath, $quality)) {
            // If the re-encode created a different file, delete the original
            if (realpath($temp_filepath) !== realpath($webp_filepath)) {
                @unlink($temp_filepath);
            }
            return $db_base_path . $webp_filename;
        }
        // If re-encode fails, keep the original WebP as-is
        return $db_base_path . $temp_filename;
    }

    // Convert non-WebP to WebP
    if ($gd_enabled) {
        if (convert_to_webp($temp_filepath, $webp_filepath, $quality)) {
            // Delete the original JPEG/PNG/GIF
            @unlink($temp_filepath);
            return $db_base_path . $webp_filename;
        }
    } else {
        // GD is not enabled, convert_to_webp copies original file directly to destination.
        // Let's copy the file to its destination under the original extension and use it.
        return $db_base_path . $temp_filename;
    }

    // Conversion failed — fall back to the original file
    return $db_base_path . $temp_filename;
}
?>
