<?php

namespace Eotvos\VersenyrBundle\Extension;

/**
 * Tells the mime type of a file based on it's extension.
 *
 * It is needed because content-based detection is sometimes wrong, e.g. docx detected as zip, instead of ms-word.
 * 
 * For more information, see the link:
 * http://stackoverflow.com/questions/1147931/how-do-i-determine-the-extensions-associated-with-a-mime-type-in-php/1147952//1147952
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @todo refactor
 * @todo make it work under windows
 */
class ExtToMime
{

    /**
     * Generates a mapping of extensions to mime types based on the posix mime.types file.
     * 
     * @return array
     */
    protected  function system_extension_mime_types()
    {
        // Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
        $out = array();
        $file = fopen('/etc/mime.types', 'r');
        while (($line = fgets($file)) !== false) {
            $line = trim(preg_replace('///.*/', '', $line));
            if (!$line) {
                continue;
            }
            $parts = preg_split('/\s+/', $line);
            if (count($parts) == 1) {
                continue;
            }
            $type = array_shift($parts);
            foreach ($parts as $part) {
                $out[$part] = $type;
            }
        }
        fclose($file);

        return $out;
    }


    /**
     * Returns a mime type based on a file name.
     * 
     * @param string $file name of the file
     * 
     * @return string mime type
     */
    public function system_extension_mime_type($file)
    {
        // Returns the system MIME type (as defined in /etc/mime.types) for the filename specified.
        //
        // $file - the filename to examine
        static $types;
        if (!isset($types)) {
            $types = $this->system_extension_mime_types();
        }
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (!$ext) {
            $ext = $file;
        }
        $ext = strtolower($ext);

        return isset($types[$ext]) ? $types[$ext] : null;
    }


    /**
     * Generates a mapping of mime types to extensions based on the posix mime.types file.
     * 
     * @return array
     */
    protected function system_mime_type_extensions()
    {
        // Returns the system MIME type mapping of MIME types to extensions, as defined in /etc/mime.types (considering the first
        // extension listed to be canonical).
        $out = array();
        $file = fopen('/etc/mime.types', 'r');
        while (($line = fgets($file)) !== false) {
            $line = trim(preg_replace('///.*/', '', $line));
            if (!$line) {
                continue;
            }
            $parts = preg_split('/\s+/', $line);
            if (count($parts) == 1) {
                continue;
            }
            $type = array_shift($parts);
            if (!isset($out[$type])) {
                $out[$type] = array_shift($parts);
            }
        }
        fclose($file);

        return $out;
    }


    /**
     * Returns a extension based on a mime type.
     * 
     * @param string $type mime type
     * 
     * @return string extension
     */
    public function system_mime_type_extension($type)
    {
        // Returns the canonical file extension for the MIME type specified, as defined in /etc/mime.types (considering the first
        // extension listed to be canonical).
        //
        // $type - the MIME type
        static $exts;
        if (!isset($exts)) {
            $exts = $this->system_mime_type_extensions();
        }

        return isset($exts[$type]) ? $exts[$type] : null;
    }

}
