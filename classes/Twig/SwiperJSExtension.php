<?php

namespace Grav\Plugin\SwiperJS\Twig;

use Grav\Common\Config\Config;
use \Grav\Common\Grav;
use Grav\Common\Page\Medium\Medium;
use Grav\Common\Page\Page;
use Grav\Common\Twig\Twig;
use Grav\Common\Utils;
use Twig_SimpleFunction;

class SwiperJSExtension extends \Twig_Extension
{
    /**
     * @var Grav
     */
    protected $grav;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * SwiperJSExtension constructor
     */
    public function __construct()
    {
        $this->grav   = Grav::instance();
        $this->config = $this->grav['config'];
        $this->twig   = $this->grav['twig'];
    }

    /**
     * Returns extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'SwiperJSExtension';
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('swiper_js', [$this, 'swiperJS']),
        ];
    }

    /**
     * @param array  $slides
     * @param string $options
     * @param string $custom_id
     *
     * @return string
     */
    public function swiperJS(array $slides, $options = [], $custom_id = null): string
    {
        if (!$slides) {
            return '';
        }

        $defaults = $this->config->get('plugins.swiper-js', []);

        if ($defaults['enabled'] === false) {
            return '';
        }

        $slidesResult = [];

        foreach ($slides as $slide) {
            $slideResult = $this->processSlideResult($slide);

            if ($slideResult) {
                $slidesResult[] = $slideResult;
            }
        }

        if (!$slidesResult) {
            return '';
        }

        $options = array_merge($defaults['swiper'], $options);

        if (!empty($options['a11y'])) {
            $options['a11y'] = $this->grav['language']->translate($options['a11y']);
        }

        $id = $custom_id ?: Utils::generateRandomString(8);
        $this->addAssets($id);

        $output = $this->twig->processTemplate(
            'partials/swiper-js/container.html.twig',
            [
                'id'      => $id,
                'slides'  => $slidesResult,
                'options' => $options,
            ]
        );

        if ($this->config->get('plugins.swiper-js.built_in_css')) {
            $this->grav['assets']->addCss('plugin://swiper-js/assets/css/swiper-js.min.css');
        }

        if ($this->config->get('plugins.swiper-js.built_in_js')) {
            $this->grav['assets']->addJs('plugin://swiper-js/assets/js/swiper-js.min.js');
        }

        $this->grav['assets']->addInlineJs(
            sprintf('pluginSwiperJS().initSwiper("%s", %s);', $id, json_encode($options)),
            ['group' => 'bottom', 'priority' => 35]
        );

        return htmlspecialchars_decode($output);
    }

    /**
     * @param $slide
     *
     * @return array|null
     */
    protected function processSlideResult($slide): ?array
    {
        if (is_array($slide)) {
            if (empty($slide['page_media']) && empty($slide['external'])) {
                $slide['type'] = 'text';

                return $slide;
            }

            if (!empty($slide['external'])) {
                $data = $this->processSlideExternal($slide['external']);

                if (!$data) {
                    return null;
                }

                $slide['type']          = 'external';
                $slide['external_data'] = $data;

                return $slide;
            }

            /** @var Page $page */
            $page  = $this->grav['page'];
            $media = $page->getMedia()->all()[$slide['page_media']] ?? null;

            if (!$media) {
                return null;
            }

            $slide['type']       = $media->type;
            $slide['page_media'] = $media;

            return $slide;
        }

        /** @var Medium $slide */

        return [
            'title'      => $slide->get('name'),
            'text'       => '',
            'type'       => $slide->type,
            'page_media' => $slide,
        ];
    }

    /**
     * @param string $external_link
     *
     * @return array|null
     */
    protected function processSlideExternal(string $external_link): ?array
    {
        $method = sprintf('processSlide%s', ucfirst($this->parseExternalService($external_link)));

        if ($method === null || !method_exists($this, $method)) {
            return null;
        }

        return $this->{$method}($external_link);
    }

    /**
     * @param string $link
     *
     * @return string|null
     */
    protected function parseExternalService(string $link): ?string
    {
        if (stripos($link, 'youtube.com/') || stripos($link, 'youtu.be/')) {
            return 'youtube';
        }

        return null;
    }

    /**
     * @param string $external_link
     *
     * @return array|null
     */
    protected function processSlideYoutube(string $external_link): ?array
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $external_link, $match)) {
            $video_id = $match[1];
        }

        if (empty($video_id)) {
            return null;
        }

        return [
            'type'      => 'youtube',
            'video_url' => $external_link,
            'video_id'  => $video_id,
        ];
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    protected function addAssets(string $id): SwiperJSExtension
    {
        $assets_path = 'plugins://swiper-js/node_modules/swiper/';

        $assets = [
            sprintf('%s%s', $assets_path, 'swiper-bundle.js'),
            sprintf('%s%s', $assets_path, 'swiper-bundle.min.css'),
        ];

        $this->grav['assets']->add($assets, ['priority' => 50]);

        // saving assets in page meta to use in pageInitialized event hook
        $this->grav['page']->addContentMeta('swiper_js_assets', $assets);

        return $this;
    }
}
