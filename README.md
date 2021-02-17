# SwiperJS Plugin

The **SwiperJS** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav),
which implements a [SwiperJS](https://swiperjs.com/) touch slider.

## Installation

Installing the SwiperJS plugin can be done in one of three ways: The GPM (Grav Package Manager) 
installation method lets you quickly install the plugin with a simple terminal command, 
the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](http://learn.getgrav.org/advanced/grav-gpm), 
through your system's terminal (also called the command line), 
navigate to the root of your Grav-installation, and enter:

    bin/gpm install swiper-js

This will install the SwiperJS plugin into your `/user/plugins`-directory within Grav. 
Its files can be found under `/your/site/grav/user/plugins/swiper-js`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository 
and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `swiper-js`. 
You can find these files on [GitHub](https://github.com/karmalakas/grav-plugin-swiper-js) 
or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/swiper-js

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly 
by browsing the `Plugins`-menu and clicking on the `Add` button.


## !Note
Currently, only manual installation available. Maybe plugin will be added to GPM in the future, but not yet.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/swiper-js/swiper-js.yaml` 
to `user/config/plugins/swiper-js.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true

# Swiper bundles are added no matter if you choose to include plugin assets
built_in_css: true              # Adds basic CSS to get you started
built_in_js: true               # Recommend to keep this enabled. Youtube videos heavily depend on included JS
                                # Otherwise you'll need to add your own `pluginSwiperJS().initSwiper(id, options)` JS method

swiper:
  autoplay: false               # autoplay Swiper
  loop: true                    # loop Swiper
  zoom: false                   # enable zoom on images
  navigation:
    nextEl: null                # Next button CSS selector (hidden if empty)
    prevEl: null                # Previous button CSS selector (hidden if empty)
  pagination:
    el: null                    # Pagination container CSS selector (hidden if empty)
  scrollbar:
    el: .swiper-scrollbar       # Scrollbar container CSS selector (hidden if empty)
  direction: horizontal         # Swiper direction - [horizontal|vertical]
  effect: slide                 # Effect - [slide|fade|cube|coverflow|flip]
  spaceBetween: 10              # Gap in px between slides
  freeMode: false               # Free swipe mode
  freeModeSticky: false         # Sticky slide in free mode
  grabCursor: true              # Show grab cursor when hovered on Swiper
  autoHeight: false             # Adjust Swiper height to each slide
  centeredSlides: false         # Center active slide
  initialSlide: 0               # Slide index to start from
  preloadImages: true           # Force all images to load
  speed: 300                    # Transition speed
  shortSwipes: true             # Enable short swipes
  keyboard:
    enabled: false              # Enables navigation through slides using keyboard
  mousewheel: false             # Enables navigation through slides using mouse wheel
  history: false                # Enables history push state where every slide will have its own url
  watchOverflow: false          # When enabled Swiper will be disabled and hide navigation buttons on case there are not enough slides for sliding
  watchSlidesProgress: false    # Enable this feature to calculate each slides progress
  watchSlidesVisibility: false  # `watchSlidesProgress` should be enabled. Enable this option and slides that are in viewport will have additional visible class
  a11y:                         # Translation strings
    firstSlideMessage: PLUGIN_SWIPER_JS.A11Y.FIRST_SLIDE
    lastSlideMessage: PLUGIN_SWIPER_JS.A11Y.LAST_SLIDE
    nextSlideMessage: PLUGIN_SWIPER_JS.A11Y.NEXT_SLIDE
    paginationBulletMessage: PLUGIN_SWIPER_JS.A11Y.PAGINATION_BULLET
    prevSlideMessage: PLUGIN_SWIPER_JS.A11Y.PREV_SLIDE
```

Note that if you use the Admin Plugin, a file with your configuration 
named swiper-js.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

Plugin adds _Swiper-js_ page template which holds custom _SwiperJS_ tab, where you can override some default settings.

This template by default uses all page media items. Images and videos are rendered in a slider. 
To override uploaded media, you can add custom items in _SwiperJS_ tab on page. 
By using these items, you can also add Youtube videos.

You can easily add Swiper to any template by using Twig extension `swiper_js()`. Default usage is:

    {% set slides = page.header.swiper.slides ?? page.media.all %}
    {{ swiper_js(slides, page.header.swiper.options)|raw }}

First argument is an array of slides, where each slide can be:
- `ImageMedium` or `VideoMedium` objects. That's what page media gives by default
- Array of:
  - `title`
  - `text`
  - `page_media` - a string, provided by Filepicker (file name)
  - `text_overlay` - boolean if Title and Text should be overlayed over image
  - `external` - Currently only Youtube video link is processed

Second argument is an object, containing any options that Swiper accepts. Check [Swiper docs](https://swiperjs.com/swiper-api#parameters)

## Credits

This is my first plugin and wouldn't be possible without the support of community (@ricardo118, @rhukster, @OleVik and others)
Also got some inspiration just by studying other plugins (too many to list)

## Known issues

- Swiping on Youtube videos doesn't work, so overlay is added. Overlay prevent video control
- If looping is enabled, sometimes Youtube videos appear only if slide is moved a bit (not always though)

If you know how to fix these or make a workaround, please feel free to submit a PR

**NB:** Tested only on Win10 Google Chrome

## To Do

- [ ] Add more supported options to configure (especially for navigation, pagination and scrollbar)
- [ ] Add shortcode support maybe (meanwhile check @OleVik's [plugin](https://github.com/OleVik/grav-plugin-swiper/))

As for now plugin fits my needs, no timeline for any of the features. If you are missing something, 
please submit an issue or create a PR.
