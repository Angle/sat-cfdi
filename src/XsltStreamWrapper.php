<?php

namespace Angle\CFDI;

use Angle\CFDI\Utility\PathUtility;

class XsltStreamWrapper
{
    const PROTOCOL = 'anglemx.sat.cfdi.xslt';

    // resource dir
    public static $RESOURCE_DIR;

    // resource filename whitelist
    public static $WHITELIST = [];

    // this will be modified by PHP to show the context passed in the current call.
    public $context;

    // this is used to store internally the requested uri
    private $uri;

    // temporal file handle (resource)
    private $handle;

    public function stream_open($uri, $mode, $options, &$opened_path)
    {
        $this->uri = $uri;

        $target = $this->getTarget();
        if (!in_array($target, self::$WHITELIST)) {
            // Target file is not in whitelist
            throw new \RuntimeException(sprintf('Target XSLT file "%s" is not configured in the XsltStreamWrapper whitelist', $target));
        }

        $path = $this->getLocalPath();

        $this->handle = $options & STREAM_REPORT_ERRORS ? fopen($path, $mode) : @fopen($path, $mode);

        if ((bool) $this->handle && $options & STREAM_USE_PATH) {
            $opened_path = $path;
        }

        return (bool) $this->handle;
    }

    public function stream_write(string $data) : int
    {
        return 0;
    }

    public function stream_read($count)
    {
        return fread($this->handle, $count);
    }

    public function stream_eof()
    {
        return feof($this->handle);
    }

    public function stream_close()
    {
        return fclose($this->handle);
    }

    // fallback exception handler if an unsupported operation is attempted.
    public function __call($name, $args)
    {
        throw new \RuntimeException("This wrapper does not support $name");
    }

    public function unlink(string $uri)
    {
        // unlink (delete) is not allowed
        return false;
    }

    public function url_stat(string $uri, int $flags) {
        $this->uri = $uri;

        $target = $this->getTarget();
        if (!in_array($target, self::$WHITELIST)) {
            // Target file is not in whitelist
            throw new \RuntimeException(sprintf('Target XSLT file "%s" is not configured in the XsltStreamWrapper whitelist', $target));
        }

        $path = $this->getLocalPath();


        // Suppress warnings if requested or if the file or directory does not
        // exist. This is consistent with PHP's plain filesystem stream wrapper.
        if ($flags & STREAM_URL_STAT_QUIET || !file_exists($path)) {
            return @stat($path);
        } else {
            return stat($path);
        }
    }

    protected function getLocalPath($uri = NULL)
    {
        if (!isset($uri)) {
            $uri = $this->uri;
        }

        if (!self::$RESOURCE_DIR || !is_readable(self::$RESOURCE_DIR)) {
            // Resources Directory misconfigured
            throw new \RuntimeException('XsltStreamWrapper resource directory misconfigured');
        }

        $path = PathUtility::join(self::$RESOURCE_DIR, $this->getTarget($uri));

        $realpath = realpath($path);

        if (!$realpath) {
            // This file does not yet exist.
            return false;
        }

        $directory = realpath(self::$RESOURCE_DIR);

        if (!$realpath || !$directory || strpos($realpath, $directory) !== 0) {
            return FALSE;
        }

        return $realpath;
    }

    protected function getTarget($uri = NULL)
    {
        if (!isset($uri)) {
            $uri = $this->uri;
        }

        list($scheme, $target) = explode('://', $uri, 2);

        // Remove erroneous leading or trailing, forward-slashes and backslashes.
        return trim($target, '\\/');
    }

    public function dirname($uri = NULL)
    {
        list($scheme, $target) = explode('://', $uri, 2);

        $target = $this->getTarget($uri);

        $dirname = dirname($target);

        if ($dirname == '.') {
            $dirname = '';
        }

        return $scheme . '://' . $dirname;
    }
}