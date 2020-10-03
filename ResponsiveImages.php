<?php

use Symfony\Component\Yaml\Parser;

/**
 * ResponsiveImages - add a shorthand to your Markdown files for responsive and lazyloading images.
 * Make sure to include https://github.com/aFarkas/lazysizes in your theme.
 * @author  Andrew Barron
 * @link    https://awmb.uk
 * @see <a href="https://github.com/aFarkas/lazysizes">LazySizes</a>
 * @see <a href="https://cdnjs.com/libraries/lazysizes">LazySizes on cdnjs</a>
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 0.1
 */
class ResponsiveImages extends AbstractPicoPlugin
{
    /**
     * API version used by this plugin
     *
     * @var int
     */
    const API_VERSION = 3;

    /**
     * Triggered after Pico has prepared the raw file contents for parsing. Replaces custom Markdown-like syntax
     * with the HTML for a lazyloaded and/or responsive image.
     *
     * @see DummyPlugin::onContentParsing()
     * @see Pico::parseFileContent()
     * @see DummyPlugin::onContentParsed()
     *
     * @param string &$markdown Markdown contents of the requested page
     */
    public function onContentPrepared(&$markdown)
    {
        do {
            // if the passed page contains a request for lazyloaded image
            if (strpos($markdown, '?[') !== false) {
                // mark we've found an image
                $imageFound = true;

                // find the start and end of the alt text
                $altStart = strpos($markdown, '?[') + 2;
                $altEnd = strpos($markdown, ']', $altStart + 1);
                // get the alt text
                $alt = substr($markdown, $altStart, $altEnd - $altStart);

                // check if there is an opening bracket for the path after the alt text
                if ($markdown[$altEnd + 1] === "(") {
                    // note the start of the path
                    $pathStart = $altEnd + 2;

                    // find the end of the paths list
                    $pathEnd = strpos($markdown, ')', $pathStart);

                    // get the path string, remove spaces, and split by comma
                    $path = substr($markdown, $pathStart, $pathEnd - $pathStart);
                    $path = preg_replace("(/,\s*/)", "", $path);
                    $pathExploded = explode(",", $path);

                    // Default image source is the first item in the path list
                    $srcFull = $pathExploded[0]; // default image source with size details
                    $src = explode(" ", $pathExploded[0])[0]; // default image source path only

                    $srcset = "";
                    // If there is more than one item in the exploded path string, set up srcset
                    if (sizeof($pathExploded) > 1) {
                        // Remove the first path from the start of the string of paths, then set the remaining paths as
                        // the srcset value
                        $srcset = "data-srcset='" . preg_replace("(/^$srcFull,/)", "", $path) . "'";
                    }

                    // Generate the image tag
                    $imageTag = <<<EOT
<noscript>
 <img src='$src' alt='$alt' width='100%' />
</noscript>
<img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='
 data-sizes='auto'
 data-src='$src'
 $srcset
  alt='$alt'
  width='100%' class='lazyload' />
EOT;

                    // replace the request with the image HTML
                    $markdown = substr_replace($markdown, $imageTag, $altStart - 2, $pathEnd - $altStart + 3);

                } else {
                    error_log("No paths found after alt text");
                }
            } else { // if no request is found
                $imageFound = false;
            }
        } while ($imageFound); // keep searching until there are no more image requests left
    }
}