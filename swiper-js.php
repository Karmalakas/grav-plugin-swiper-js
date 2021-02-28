<?php

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Plugin;
use Grav\Plugin\SwiperJS\Twig\SwiperJSExtension;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class SwiperJSPlugin
 *
 * @package Grav\Plugin
 */
class SwiperJSPlugin extends Plugin
{
    /** @var array */
    public $features = [
        'blueprints' => 0, // Use priority 0
    ];

    /**
     * Composer autoload.
     *is
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['onPluginsInitialized', 0]
            ],
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        if ($this->isAdmin()) {
            $this->enable(
                [
                    'onAdminSave'         => ['onAdminSave', 0],
                    'onGetPageTemplates'  => ['onGetPageTemplates', 0],
                    'onGetPageBlueprints' => ['onGetPageBlueprints', 0],
                ]
            );

            return;
        }

        $this->enable(
            [
                'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
                'onTwigExtensions'    => ['onTwigExtensions', 0],
            ]
        );
    }

    /**
     * @param $event
     */
    public function onAdminSave($event)
    {
        $page = $event['object'];

        if (!$page instanceof PageInterface) {
            return;
        }

        $header  = $page->header();

        if (!isset($header['swiper'])) {
            return;
        }

        $options = $header['swiper']['options'] ?? [];

        $header->offsetSet('swiper.options.navigation.nextEl', $options['navigation']['nextEl'] ?? '');
        $header->offsetSet('swiper.options.navigation.prevEl', $options['navigation']['prevEl'] ?? '');
        $header->offsetSet('swiper.options.pagination.el', $options['pagination']['el'] ?? '');
        $header->offsetSet('swiper.options.scrollbar.el', $options['scrollbar']['el'] ?? '');
    }

    /**
     * Add blueprint directory to page templates.
     */
    public function onGetPageTemplates(Event $event)
    {
        $locator = Grav::instance()['locator'];
        $event->types->scanTemplates($locator->findResource('plugin://' . $this->name . '/templates'));
    }

    /**
     * Extend page blueprints with additional configuration options.
     *
     * @param Event $event
     */
    public function onGetPageBlueprints($event)
    {
        $locator = Grav::instance()['locator'];
        $event->types->scanBlueprints($locator->findResource('plugin://' . $this->name . '/blueprints'));
    }

    /**
     * Register templates
     *
     * @return void
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * @return void
     */
    public function onTwigExtensions()
    {
        $this->grav['twig']->twig->addExtension(new SwiperJSExtension());
    }
}
