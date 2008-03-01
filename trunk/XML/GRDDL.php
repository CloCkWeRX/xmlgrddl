<?php
class XML_GRDDL
{
    const NS       = "http://www.w3.org/2003/g/data-view#";
    const XHTML_NS = 'http://www.w3.org/1999/xhtml';

    /**
     * Instantiate a new instance of a GRDDL driver.
     *
     * @param string  $driver  Name of driver. Default is 'xsl'.
     * @param mixed[] $options An array of options, refer to individual drivers
     *                         document for more information.
     *
     * @return  XML_GRDDL_Driver
     */
    public static function factory($driver = 'xsl',
                                   $options = array('documentTransformations' => true,
                                                    'htmlProfileTransformations' => true,
                                                    'namespaceTransformations' => true))
    {
        $class = 'XML_GRDDL_Driver_' . $driver;

        $path = dirname(__FILE__) . '/GRDDL/Driver/' . $driver . '.php';

        if (file_exists($path)) {
            include_once $path;
        }

        if (!class_exists($class)) {
            throw new Exception("Unknown driver " . $class);
        }

        return new $class($options);
    }
}