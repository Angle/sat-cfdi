<?php

namespace Angle\CFDI;

use DOMDocument;

use Genkgo\Xsl\Transpiler;
use Genkgo\Xsl\Cache\NullCache;
use Genkgo\Xsl\Cache\ArrayCache;
use Genkgo\Xsl\Callback\FunctionCollection;
use Genkgo\Xsl\TransformationContext;
use Genkgo\Xsl\Util\TransformerCollection;

use Angle\CFDI\Utility\PathUtility;

class XsltStreamWrapper
{
    const PROTOCOL = 'anglemx.sat.cfdi.xslt';

    // resource dir
    public static $RESOURCE_DIR;

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

        $path = $this->getLocalPath();

        $this->handle = ($options & STREAM_REPORT_ERRORS) ? fopen($path, $mode) : @fopen($path, $mode);

        if ((bool) $this->handle && $options & STREAM_USE_PATH) {
            $opened_path = $path;
        }


        // File exists and was opened, we can perform a transpilation now

        ///// XSLT TRANSPILATION 2.0 -> 1.0 /////
        // Initialize a basic Transpiler
        // TODO: can this be optimized? this will be initialized on every new call..
        $transpiler = new Transpiler(
            new TransformationContext(new DOMDocument('1.0', 'UTF-8'), new TransformerCollection(), new FunctionCollection()),
            new NullCache() // TODO: Enable some kind of cache.. see psr/simple-cache
        );

        try {
            $transpiledString = $transpiler->transpileFile($path);
        } catch (\DOMException $e) {
            // Stylesheet could not be parsed
            // TODO: error handling
            return false;
        }

        // Finally, we'll create a new "stream" from a in-memory string, and this is what we'll return
        $this->handle = fopen('php://temp', 'r+');
        fwrite($this->handle, $transpiledString);
        rewind($this->handle);

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