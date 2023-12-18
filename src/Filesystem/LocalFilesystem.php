<?php

namespace Takemo101\Chubby\Filesystem;

/**
 * Local Filesystem
 */
interface LocalFilesystem
{
    /**
     * File exists?
     *
     * @param string $path
     * @return boolean
     */
    public function exists(string $path): bool;

    /**
     * Load file.
     * Get null if it cannot be read.
     *
     * @param string $path
     * @return null|string
     */
    public function read(string $path): ?string;

    /**
     * File write (overwrite)
     *
     * @param string $path
     * @param string|resource $content
     * @return boolean
     */
    public function write(string $path, $content): bool;

    /**
     * Additional file writing  (to the top)
     *
     * @param string $path
     * @param string $content
     * @return boolean
     */
    public function prepend(string $path, string $content): bool;

    /**
     * Additional file writing (to the end)
     *
     * @param string $path
     * @param string $content
     * @return boolean
     */
    public function append(string $path, string $content): bool;

    /**
     * Delete file.
     *
     * @param string $path
     * @return boolean
     */
    public function delete(string $path): bool;

    /**
     * Change file permissions.
     *
     * @param string $path
     * @param integer $permission
     * @param boolean $recursive
     * @return boolean
     */
    public function chmod(string $path, int $permission = 0o755, bool $recursive = true): bool;

    /**
     * Copy file.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function copy(string $from, string $to): bool;

    /**
     * Move file.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function move(string $from, string $to): bool;

    /**
     * Symbolic link.
     *
     * @param string $target
     * @param string $link
     * @return boolean
     */
    public function symlink(string $target, string $link): bool;

    /**
     * Symbolic relative link.
     *
     * @param string $target
     * @param string $link
     * @return boolean
     */
    public function relativeSymlink(string $target, string $link): bool;

    /**
     * Hard link.
     *
     * @param string $target
     * @param string $link
     * @throws LocalFilesystemException
     * @return boolean
     */
    public function hardlink(string $target, string $link): bool;

    /**
     * Obtain link destinations for symbolic links.
     *
     * @param string $path
     * @return null|string
     */
    public function readlink(string $path): ?string;

    /**
     * Get the normalized absolute path.
     *
     * @param string $path
     * @return null|string
     */
    public function realpath(string $path): ?string;

    /**
     * Get file size.
     *
     * @param string $path
     * @return integer
     * @throws LocalFilesystemException
     */
    public function size(string $path): int;

    /**
     * Get the file's modification timestamp.
     *
     * @param string $path
     * @return integer
     * @throws LocalFilesystemException
     */
    public function time(string $path): int;

    /**
     * Is the specified path a file?
     *
     * @param string $path
     * @return boolean
     */
    public function isFile(string $path): bool;

    /**
     * Is the specified path a directory?
     *
     * @param string $path
     * @return boolean
     */
    public function isDirectory(string $path): bool;

    /**
     * Is the specified path a symbolic link?
     *
     * @param string $path
     * @return boolean
     */
    public function isLink(string $path): bool;

    /**
     * Is the specified path readable?
     *
     * @param string $path
     * @return boolean
     */
    public function isReadable(string $path): bool;

    /**
     * Is the specified path writable?
     *
     * @param string $path
     * @return bool
     */
    public function isWritable(string $path): bool;

    /**
     * Obtain permissions for files in the specified path.
     *
     * @param string $path
     * @return null|integer
     */
    public function permission(string $path): ?int;

    /**
     * Get file type.
     *
     * @param string $path
     * @return null|string
     */
    public function type(string $path): ?string;

    /**
     * Get file mimetype
     *
     * @param string $path
     * @return null|string
     */
    public function mimeType(string $path): ?string;

    /**
     * Get a hash of the file path.
     *
     * @param  string  $path
     * @param  string  $algorithm
     * @return string
     */
    public function hash(string $path, string $algorithm = 'md5');

    /**
     * Find pathnames that match a pattern and return them as an array.
     *
     * @param string $pattern
     * @return null|string[]
     */
    public function glob(string $pattern): ?array;

    /**
     * Create directory.
     *
     * @param string $path
     * @param integer $permission
     * @param boolean $recursive
     * @return boolean
     */
    public function makeDirectory(string $path, int $permission = 0o755, bool $recursive = true): bool;

    /**
     * Move directory.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function moveDirectory(string $from, string $to): bool;

    /**
     * Copy directory.
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function copyDirectory(string $from, string $to): bool;

    /**
     * Delete directory.
     *
     * @param string $path
     * @return boolean
     */
    public function deleteDirectory(string $path): bool;

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @return mixed
     * @throws LocalFilesystemException
     */
    public function require(string $path): mixed;
}
