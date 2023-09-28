<?php

namespace MatthiasWeb\RealMediaLibrary\Vendor\MatthiasWeb\Utils;

// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Base i18n management for backend and frontend for a package.
 * For non-utils packages you need to extend from this class and
 * properly fill the constructor.
 */
class PackageLocalization
{
    use Localization;
    private $rootSlug;
    private $packageDir;
    private $packageInfo = null;
    /**
     * C'tor.
     *
     * @param string $rootSlug Your workspace scope name.
     * @param string $packageDir Absolute path to your package.
     * @codeCoverageIgnore
     */
    protected function __construct($rootSlug, $packageDir)
    {
        $this->rootSlug = $rootSlug;
        $this->packageDir = \trailingslashit($packageDir);
    }
    /**
     * Get the directory where the languages folder exists.
     *
     * @param string $type
     * @return string[]
     */
    protected function getPackageInfo($type)
    {
        if ($this->packageInfo === null) {
            $textdomain = $this->getRootSlug() . '-' . $this->getPackage();
            if ($type === Constants::LOCALIZATION_BACKEND) {
                $this->packageInfo = [$this->getPackageDir() . 'languages/backend', $textdomain, $this->getPackage()];
            } else {
                $this->packageInfo = [$this->getPackageDir() . 'languages/frontend/json', $textdomain, $this->getPackage()];
            }
        }
        return $this->packageInfo;
    }
    /**
     * Getter.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getRootSlug()
    {
        return $this->rootSlug;
    }
    /**
     * Get package name.
     *
     * @return string
     */
    public function getPackage()
    {
        return \basename($this->getPackageDir());
    }
    /**
     * Getter.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getPackageDir()
    {
        return $this->packageDir;
    }
    /**
     * New instance.
     *
     * @param string $rootSlug
     * @param string $packageDir
     * @return PackageLocalization
     * @codeCoverageIgnore Instance getter
     */
    public static function instance($rootSlug, $packageDir)
    {
        return new PackageLocalization($rootSlug, $packageDir);
    }
    /**
     * Get the `/languages` folder which is directly located under the plugins path.
     *
     * @param string $path
     * @param boolean $appendFile
     */
    public static function getParentLanguageFolder($path, $appendFile = \false)
    {
        $pluginFilePath = \constant('WP_PLUGIN_DIR') . '/' . \explode('/', \plugin_basename($path))[0];
        // Disable this in our local development environment
        if (@\is_dir($pluginFilePath . '/public/ts')) {
            return \untrailingslashit($path);
        }
        $pluginFilePath .= '/languages/';
        return \untrailingslashit(@\is_dir($pluginFilePath) ? $pluginFilePath . ($appendFile ? \basename($path) : '') : $path);
    }
}
