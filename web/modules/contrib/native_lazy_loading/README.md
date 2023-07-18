# Native Lazy Loading

## Quick description

Ensure images are natively lazy-loaded by browsers supporting the loading='lazy' img attribute.

## Context

Image lazy-loading is a common performance improvement, especially for phones with low bandwidth. Of course performance is good for UX and also for SEO. But it used to be a challenging feature which needed powerful javascript libraries such as bLazy to alter the HTML when the user scrolled down.

Now, a new feature is available on most browsers (70% and more coming): native lazy-loading, handled by the browser. It only needs a declarative attribute on img tags: loading='lazy'. This simple module adds the attribute on all Drupal image formatters, responsive or not.

About compatible browsers: https://caniuse.com/#feat=loading-lazy-attr

## When to use it

If it's OK for you not to have lazy-loading on older browsers, use it instead of other modules which leverage javascript libraries as a fallback for unsupported browsers, such as Blazy or Lazy-load.
Then you will keep your website simple and avoid problems such as:
- various incompatibilities with other image handling modules (SVG and webp support, manual crops, default image,...),
- JS bugs breaking other features,
- problems related to images in CKEditor fields, with their caption and alignment,
- problems related to image styles and their various image effects,...

Note that Google Lighthouse SEO tool now takes native lazy-loading into account. So don't worry, you'll get a good rating.

## Troubleshooting

To prevent a layout shift when the image is not yet loaded, we make sure to tell the browser about the image aspect ratio, by setting width and height attributes if not already set.
See these links to understand why:
https://www.smashingmagazine.com/2020/03/setting-height-width-images-imp...
https://n8d.at/native-lazy-loading-images-with-aspect-ratio/

For responsive images, if CSS tells nothing about width and height, the sizes attribute would have determined the displayed size of the image. It won't anymore, but that's not a problem because it's not its role: sizes is for choosing the right src file for the current device/viewport). Use CSS if you want to display an image at a size which is not the one of the file.
