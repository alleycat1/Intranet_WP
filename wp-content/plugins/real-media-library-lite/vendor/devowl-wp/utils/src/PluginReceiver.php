<?php

namespace MatthiasWeb\RealMediaLibrary\Vendor\MatthiasWeb\Utils;

/**
 * This trait is used via Base trait and allows to consume
 * the prefix filled but the UtilsProvider in your plugin. That allows
 * you to dynamically get plugin data within your composer package.
 */
trait PluginReceiver
{
    /**
     * Get a value from a defined constant with no prefix.
     * The prefix is plugin relevant.
     *
     * @param string $name
     * @return null|mixed|string
     */
    public function getPluginConstant($name = null)
    {
        $prefix = $this->getPluginConstantPrefix();
        if ($name === null) {
            return $prefix;
        }
        $cname = $prefix . '_' . $name;
        return \defined($cname) ? \constant($cname) : null;
    }
    /**
     * Get a new instance of a plugin class from string (supports namespaces, too).
     *
     * @param string $name
     * @param mixed $parameter,... Parameters to the method
     * @return mixed
     */
    public function getPluginClassInstance($name)
    {
        $fqn = $this->getPluginConstant(Constants::PLUGIN_CONST_NS) . '\\' . $name;
        $parameters = \array_slice(\func_get_args(), 1);
        return new $fqn(...$parameters);
    }
    /**
     * Get the functions instance.
     *
     * @return mixed
     */
    public function getCore()
    {
        return \call_user_func([$this->getPluginConstant(Constants::PLUGIN_CONST_NS) . '\\Core', 'getInstance']);
    }
    /**
     * Get the plugins' constant prefix. Will be overwritten by the UtilsProvider class.
     *
     * @return string
     */
    public function getPluginConstantPrefix()
    {
        if (\defined('CONSTANT_PREFIX')) {
            return \constant('CONSTANT_PREFIX');
        } else {
            // @codeCoverageIgnoreStart
            \error_log(__FILE__ . ': Something went wrong with a newly installed plugin.');
            exit(1);
            // @codeCoverageIgnoreEnd
        }
    }
}
