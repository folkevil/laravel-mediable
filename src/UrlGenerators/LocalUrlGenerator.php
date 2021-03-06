<?php

namespace Plank\Mediable\UrlGenerators;

use Plank\Mediable\Exceptions\MediaUrlException;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Routing\UrlGenerator as Url;

/**
 * Local Url Generator
 *
 * @author Sean Fraser <sean@plankdesign.com>
 */
class LocalUrlGenerator extends BaseUrlGenerator
{

    /**
     * @var Url
     */
    protected $url;

    /**
     * Constructor
     * @param Config $config
     * @param Url    $url
     */
    public function __construct(Config $config, Url $url)
    {
        parent::__construct($config);
        $this->url = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function isPubliclyAccessible()
    {
        return strpos($this->getAbsolutePath(), public_path()) === 0;
    }

    /**
     * Get the path to relative to the webroot
     * @throws MediaUrlException If media's disk is not publicly accessible
     * @return string
     */
    public function getPublicPath()
    {
        if (!$this->isPubliclyAccessible()) {
            throw MediaUrlException::mediaNotPubliclyAccessible($this->getAbsolutePath(), public_path());
        }
        $path = str_replace(public_path(), '', $this->getAbsolutePath());

        return $this->cleanDirectorySeparators($path);
    }

    /**
     * {@inheritDoc}
     * @throws MediaUrlException If media's disk is not publicly accessible
     */
    public function getUrl()
    {
        return $this->url->asset($this->getPublicPath());
    }

    /**
     * {@inheritDoc}
     */
    public function getAbsolutePath()
    {
        return $this->getDiskConfig('root') . DIRECTORY_SEPARATOR . $this->media->getDiskPath();
    }


    /**
     * Correct directory separator slashes on non-unix systems
     * @param  string $path
     * @return string
     */
    protected function cleanDirectorySeparators($path)
    {
        if (DIRECTORY_SEPARATOR != '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        return $path;
    }
}
