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
        $imageFound = false;
        do {
            // if the passed page contains a request for image
            if (strpos($markdown, '[img|') !== false) {
                // mark we've found an image
                $imageFound = true;

                // find the start and end of the responsive image request
                $start = strpos($markdown, '[img|');
                $end = strpos($markdown, ']', $start + 1);

                // get the image request and split it by |
                $request = substr($markdown, $start, $end - $start);
                $requestSplit = explode("|", $request);

                // if there are 4 parameters to the request
                if (sizeof($requestSplit) == 4) {
                    // Create the code for the image
                    $responsiveLazyloadingImageTags = <<<EOT
<noscript>
 <img src='$requestSplit[1]' alt='$requestSplit[3]' width='100%' />
</noscript>
<img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='
 data-sizes='auto'
 data-src='$requestSplit[1]'
 data-srcset='$requestSplit[2]'
  alt='$requestSplit[3]'
  width='100%' class='lazyload' />
EOT;

                    // replace the request with a responsive, lazyloading image with multiple sizes
                    $markdown = substr_replace($markdown, $responsiveLazyloadingImageTags, $start, $end - $start + 1);
                } else if (sizeof($requestSplit) == 3) {
                    // Create the code for the image
                    $lazyloadingImageTags = <<< EOT
<noscript>
 <img src='$requestSplit[1]' alt='$requestSplit[2]' width='100%' />
</noscript>
<img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='
 data-src='$requestSplit[1]'
 alt='$requestSplit[2]'
 width='100%' class='lazyload' />
EOT;

                    // replace the request with a lazyloading image image
                    $markdown = substr_replace($markdown, $lazyloadingImageTags, $start, $end - $start + 1);
                } else { // malformed request
                    // mark we've found an image
                    $imageFound = true;

                    // find the start and end of the responsive image request
                    $start = strpos($markdown, '[img|');
                    $end = strpos($markdown, ']', $start + 1);

                    // replace the bad request with a nothing - stops us trying to change it again
                    $markdown = substr_replace($markdown, '', $start, $end - $start + 1);

                    error_log("Malformed request for responsive image.");
                }
            } else { // if no request is found
                $imageFound = false;
            }
        } while ($imageFound); // keep searching until there are no more image requests left
    }
}