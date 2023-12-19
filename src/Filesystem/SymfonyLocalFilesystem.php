<?php

namespace Takemo101\Chubby\Filesystem;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Takemo101\Chubby\Filesystem\Mime\FinfoMimeTypeGuesser;
use Takemo101\Chubby\Filesystem\Mime\MimeTypeGuesser;
use SplFileInfo;

/**
 * Local Filesystem
 *
 * reference: https://github.com/illuminate/filesystem
 */
class SymfonyLocalFilesystem implements LocalFilesystem
{
    /**
     * @var SymfonyFilesystem
     */
    private SymfonyFilesystem $fs;

    /**
     * @var MimeTypeGuesser<SplFileInfo>
     */
    private MimeTypeGuesser $mimeTypeGuesser;

    /**
     * constructor
     *
     * @param MimeTypeGuesser<SplFileInfo>|null $mimeTypeGuesser
     */
    public function __construct(
        ?MimeTypeGuesser $mimeTypeGuesser = null
    ) {
        $this->fs = new SymfonyFilesystem();
        $this->mimeTypeGuesser = $mimeTypeGuesser ?? new FinfoMimeTypeGuesser();
    }

    /**
     *  ファイル存在
     *
     * @param string $path
     * @return boolean
     */
    public function exists(string $path): bool
    {
        return $this->fs->exists($path);
    }

    /**
     * Load file.
     * Get null if it cannot be read.
     *
     * @param string $path
     * @throws LocalFilesystemException
     * @return null|string
     */
    public function read(string $path): ?string
    {
        if ($this->isFile($path)) {
            $content = file_get_contents($path);
            return $content === false ? null : $content;
        }

        throw LocalFilesystemException::ioError($path);
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
        try {
            $this->fs->dumpFile($path, $content);
        } catch (IOException) {
            return false;
        }

        return true;
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
        try {
            $this->fs->appendToFile($path, $content);
        } catch (IOException) {
            return false;
        }

        return true;
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
            $this->fs->remove($path);
        } catch (IOException) {
            return false;
        }

        return true;
    }

    /**
     * Change file permissions.
     *
     * @param string $path
     * @param integer $permission
     * @param boolean $recursive
     * @return boolean
     */
    public function chmod(string $path, int $permission = 0o755, bool $recursive = false): bool
    {
        try {
            $this->fs->chmod(
                files: $path,
                mode: $permission,
                recursive: $recursive,
            );
        } catch (IOException) {
            return false;
        }

        return true;
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
        try {
            $this->fs->copy($from, $to);
        } catch (IOException) {
            return false;
        }

        return true;
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
        try {
            $this->fs->rename($from, $to);
        } catch (IOException) {
            return false;
        }

        return true;
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
        try {
            $this->fs->symlink($target, $link);
        } catch (IOException) {
            return false;
        }

        return true;
    }

    /**
     * Symbolic relative link.
     *
     * @param string $target
     * @param string $link
     * @return boolean
     */
    public function relativeSymlink(string $target, string $link): bool
    {
        $relativeTarget = $this->fs->makePathRelative($target, dirname($link));

        return $this->symlink($this->isFile($target) ? rtrim($relativeTarget, '/') : $relativeTarget, $link);
    }

    /**
     * Hard link.
     *
     * @param string $target
     * @param string $link
     * @return boolean
     */
    public function hardlink(string $target, string $link): bool
    {
        try {
            $this->fs->hardlink($target, $link);
        } catch (FileNotFoundException $e) {
            throw LocalFilesystemException::notFound($e->getPath() ?? $target);
        } catch (IOException) {
            return false;
        }

        return true;
    }

    /**
     * Obtain link destinations for symbolic links.
     *
     * @param string $path
     * @return null|string
     */
    public function readlink(string $path): ?string
    {
        return $this->fs->readlink($path);
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
     * @throws LocalFilesystemException
     */
    public function size(string $path): int
    {
        if ($size = filesize($path)) {
            return $size;
        }

        throw LocalFilesystemException::ioError($path);
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

        throw LocalFilesystemException::ioError($path);
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
        return $this->mimeTypeGuesser->guess(new SplFileInfo($path));
    }

    /**
     * Get a hash of the file path.
     *
     * @param string $path
     * @param string $algorithm
     * @return string
     */
    public function hash(string $path, string $algorithm = 'md5')
    {
        return hash_file($algorithm, $path);
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
        try {
            $this->fs->rename($from, $to);
        } catch (IOException) {
            return false;
        }

        return true;
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
        try {
            $this->fs->mirror($from, $to);
        } catch (IOException) {
            return false;
        }

        return true;
    }

    /**
     * Delete directory.
     *
     * @param string $path
     * @return boolean
     */
    public function deleteDirectory(string $path): bool
    {
        try {
            $this->fs->remove($path);
        } catch (IOException) {
            return false;
        }

        return true;
    }

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @return mixed
     */
    public function require(string $path): mixed
    {
        if ($this->isFile($path)) {
            return require($path);
        }

        throw LocalFilesystemException::notFound($path);
    }
}
