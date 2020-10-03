# PicoResponsiveImages
This is a plugin for [Pico CMS](http://picocms.org) which provides a Markdown-like syntax to include lazy loaded images 
in your pages, powered by [aFarkas/lazysizes](https://github.com/aFarkas/lazysizes). You can also include multiple 
sizes of source image per image element -- the user's browser then chooses the best one to download and display based 
on the conditions.

It's a convenient way to [lazy load your images](https://developer.mozilla.org/en-US/docs/Web/Performance/Lazy_loading), 
and make use of the [HTML5 `srcset` attribute](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images).

## Install
1. This plugin is effectively a nice abstraction within Markdown for 
[aFarkas/lazysizes](https://github.com/aFarkas/lazysizes), so make sure to include lazysizes in your theme file. The 
easiest way to do it is by adding a script tag from [cdnjs](https://cdnjs.com/libraries/lazysizes) to your theme file, 
just before the closing `<body>` tag.
2. [Download the latest release of this plugin](https://github.com/wlabarron/PicoResponsiveImages/releases) and decompress 
the folder to your Pico plugins directory.

If you're looking for ways of adding a loading animation to your images, check the 
[lazysizes repo](https://github.com/aFarkas/lazysizes).

## Usage
All images added with this plugin's syntax have 100% width. They also have a `<noscript>` fallback, in case the user has 
JavaScript disabled. 

### Lazy loading images
Include an image using the standard Markdown syntax, but swap `!` for `?`.

`![A cat on a mat](/assets/cat.png)` becomes `?[A cat on a mat](/assets/cat.png)`. Done! 

### Lazy loading responsive images
Include an image using the standard Markdown syntax, but swap `!` for `?`, and provide a comma-separated list of paths 
and sizes or resolutions. The first path provided is the "default" in case the browser doesn't support the `src` 
attribute, or if JavaScript is disabled.

For example:
* `?[A cat on a mat](/assets/cat-500w.png 500w, /assets/cat-1000w.png 1000w)`
* `?[A cat on a mat](/assets/cat-1x.png 1x, /assets/cat-2x.png 2x)`