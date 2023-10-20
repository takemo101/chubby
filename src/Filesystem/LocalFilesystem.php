<?php

namespace Takemo101\Chubby\Filesystem;

use ErrorException;

/**
 * Local Filesystem
 */
final readonly class LocalFilesystem implements LocalSystem
{
    /**
     * @var PathHelper
     */
    public PathHelper $helper;

    /**
     * constructor
     *
     * @param PathHelper|null $helper
     */
    public function __construct(
        ?PathHelper $helper = null,
    ) {
        $this->helper = $helper ?? new PathHelper();
    }

    /**
     * File exists?
     *
     * @return PathHelper
     */
    public function helper(): PathHelper
    {
        return $this->helper;
    }

    /**
     *  ファイル存在
     *
     * @param string $path
     * @return boolean
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Load file.
     * Get null if it cannot be read.
     *
     * @param string $path
     * @throws LocalSystemException
     * @return null|string
     */
    public function read(string $path): ?string
    {
        if ($this->isFile($path)) {
            $content = file_get_contents($path);
            return $content === false ? null : $content;
        }

        throw new LocalSystemException("does not exist at path [{$path}]");
    }

    /**
     * File write (overwrite)
     *
     * @param string $path
     * @param string|resource $content
     * @return boolean
     */
    public function write(string $path, $content): bool
    {
        return (bool)file_put_contents($path, $content);
    }

    /**
     * Additional file writing  (to the top)
     *
     * @param string $path
     * @param string $content
     * @return boolean
     */
    public function prepend(string $path, string $content): bool
    {
        if ($this->exists($path)) {
            return $this->write($path, $content . $this->read($path));
        }

        return $this->write($path, $content);
    }

    /**
     * Additional file writing (to the end)
     *
     * @param string $path
     * @param string $content
     * @return boolean
     */
    public function append(string $path, string $content): bool
    {
        return (bool)file_put_contents($path, $content, FILE_APPEND);
    }

    /**
     * Delete file.
     *
     * @param string $path
     * @return boolean
     */
    public function delete(string $path): bool
    {
        try {
            $result = @unlink($path);
        } catch (ErrorException $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Change file permissions.
     *
     * @param string $path
     * @param integer $permission
     * @return boolean
     */
    public function chmod(string $path, int $permission = 0o755): bool
    {
        return chmod($path, $permission);
    }

    /**
     * Copy file.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function copy(string $from, string $to): bool
    {
        return copy($from, $to);
    }

    /**
     * Move file.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function move(string $from, string $to): bool
    {
        return rename($from, $to);
    }

    /**
     * Symbolic link.
     *
     * @param string $target
     * @param string $link
     * @return boolean
     */
    public function symlink(string $target, string $link): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return false;
        }

        return symlink($target, $link);
    }

    /**
     * Obtain link destinations for symbolic links.
     *
     * @param string $path
     * @throws LocalSystemException
     * @return null|string
     */
    public function readlink(string $path): ?string
    {
        if ($this->exists($path) && $this->isLink($path)) {
            $link = readlink($path);

            return $link ? $link : null;
        }

        throw new LocalSystemException("does not exist or link at path [{$path}]");
    }

    /**
     * Get the normalized absolute path.
     *
     * @param string $path
     * @return null|string
     */
    public function realpath(string $path): ?string
    {
        $path = realpath($path);

        return $path ? $path : null;
    }

    /**
     * Get file size.
     *
     * @param string $path
     * @return integer
     * @throws LocalSystemException
     */
    public function size(string $path): int
    {
        if ($size = filesize($path)) {
            return $size;
        }

        throw new LocalSystemException("does not exist at path [{$path}]");
    }

    /**
     * Get the file's modification timestamp.
     *
     * @param string $path
     * @return integer
     */
    public function time(string $path): int
    {
        if ($time = filemtime($path)) {
            return $time;
        }

        throw new LocalSystemException("does not exist at path [{$path}]");
    }

    /**
     * Is the specified path a file?
     *
     * @param string $path
     * @return boolean
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Is the specified path a directory?
     *
     * @param string $path
     * @return boolean
     */
    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Is the specified path a symbolic link?
     *
     * @param string $path
     * @return boolean
     */
    public function isLink(string $path): bool
    {
        return is_link($path);
    }

    /**
     * Is the specified path readable?
     *
     * @param string $path
     * @return boolean
     */
    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    /**
     * Is the specified path writable?
     *
     * @param string $path
     * @return bool
     */
    public function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    /**
     * Extract meta-information for a file at a specified path.
     *
     * @param string $path
     * @param integer $option
     * @throws LocalSystemException
     * @return string|array{dirname?:string,basename:string,extension?:string,filename:string}
     */
    public function extract(string $path, int $option = PATHINFO_BASENAME): string|array
    {
        return pathinfo($path, $option);
    }

    /**
     * Obtain permissions for files in the specified path.
     *
     * @param string $path
     * @return null|integer
     */
    public function permission(string $path): ?int
    {
        $result = fileperms($path);

        return $result === false ? null : $result;
    }

    /**
     * Get file type.
     *
     * @param string $path
     * @return null|string
     */
    public function type(string $path): ?string
    {
        $result = filetype($path);

        return $result === false ? null : $result;
    }

    /**
     * Get file mimetype
     *
     * @param string $path
     * @return null|string
     */
    public function mimeType(string $path): ?string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return null;
        }

        $result = finfo_file($finfo, $path);

        return $result === false ? null : $result;
    }

    /**
     * Find pathnames that match a pattern and return them as an array.
     *
     * @param string $pattern
     * @return null|string[]
     */
    public function glob(string $pattern): ?array
    {
        $result = glob($pattern);

        return $result === false ? null : $result;
    }

    /**
     * Create directory.
     *
     * @param string $path
     * @param integer $permission
     * @param boolean $recursive
     * @return boolean
     */
    public function makeDirectory(string $path, int $permission = 0o755, bool $recursive = true): bool
    {
        return mkdir($path, $permission, $recursive);
    }

    /**
     * Move directory.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function moveDirectory(string $from, string $to): bool
    {
        return @rename($from, $to) === true;
    }

    /**
     * Copy directory.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function copyDirectory(string $from, string $to): bool
    {
        if (!$this->isDirectory($from)) {
            return false;
        }

        $this->makeDirectory($to, 0o777);

        $paths = $this->glob($this->helper->join($from, "*"));

        if ($paths === null) {
            return false;
        }

        foreach ($paths as $path) {
            /** @var string */
            $target = $this->extract($path);

            $target = $this->helper->join($to, $target);

            if ($this->isDirectory($path)) {
                return $this->copyDirectory($path, $target);
            }

            if (!$this->copy($path, $target)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete directory.
     *
     * @param string $path
     * @param boolean $keep
     * @return boolean
     */
    public function deleteDirectory(string $path, bool $keep = true): bool
    {
        if (!$this->isDirectory($path)) {
            return false;
        }

        $paths = $this->glob($this->helper->join($path, "*"));

        if ($paths === null) {
            return false;
        }

        foreach ($paths as $target) {
            if ($this->isDirectory($target)) {
                if (!$this->deleteDirectory($target, $keep)) {
                    return false;
                }
            } elseif (!$this->delete($target)) {
                return false;
            }
        }

        if (!$keep) {
            rmdir($path);
        }

        return true;
    }
}
