# JantaoDevSitemapBundle

This Symfony bundle provides sitemap XML generation.

Features:

- advanced robots.txt configuration
- several hosts support
- optional sitemap GZip compression
- sitemap constraints (10 MBytes/50 000 items per file) support
- route parameters iteration

Requirements:

- PHP 7.2
- Symfony 5
- Doctrine 2

## 1. Installation

Run `composer require jantaodev/sitemap-bundle`.

Register the bundle in the `app/AppKernel.php` file (optional):

```php
...
public function registerBundles()
{
    $bundles = array(
        ...
        new JantaoDev\SitemapBundle\JantaoDevSitemapBundle(),
        ...
    );
...
```

Add host name in `app/config/parameters.yml`

```yaml
parameters:
    ...
    router.request_context.host:   example.com
    router.request_context.scheme: http
```

or in `app/config/config.yml`

```yaml
...
jantao_dev_sitemap:
    hosts:
        - example.com
```

Now you can use the bundle.

## 2. Basic usage

There are several ways to add route to sitemap.

### 2.1. Annotation, yaml, XML (for static routes without parameters)

Annotation simple example:

```php
/**
 * @Route("/", name="homepage", options={"sitemap" = true})
 */
```

Yaml simple example:

```yaml
homepage:
    path: /
    defaults: { _controller: "AppBundle:Default:index" }
    options:
        sitemap: true
```

XML simple example:

```xml
<route id="homepage" path="/">
    <default key="_controller">AppBundle:Default:index</default>
    <option key="sitemap">true</option>
</route>
```

Annotation full example:

```php
/**
 * @Route("/", name="homepage", options={"priority" = 0.5, "changeFreq" = "monthly", "lastMod" = "2017-02-23T13:14:15+02:00" })
 */
```

Yaml full example:

```yaml
homepage:
    path: /
    defaults: { _controller: "AppBundle:Default:index" }
    options:
        sitemap:
            priority: 0.5
            changeFreq: monthly
            lastMod: "2017-02-23T13:14:15+02:00"
```

XML full example:

```xml
<route id="homepage" path="/">
    <default key="_controller">AppBundle:Default:index</default>
    <option key="sitemap">
        {"priority":"0.5", "changeFreq":"monthly", "lastMod":"2017-02-23T13:14:15+02:00"}
    </option>
</route>
```

There are three parameters:

- priority (real number from 0 to 1)
- changeFreq (one of values: always, never, hourly, daily, weekly, monthly, yearly)
- lastMod (see [http://php.net/manual/en/datetime.formats.php](http://php.net/manual/en/datetime.formats.php))

It`s not necessary to specify all parameters.

### 2.2. Bundle configuration in app/config/config.yml

Simple example for static route:

```yaml
jantao_dev_sitemap:
    ...
    sitemap:
        homepage: ~
```

Full example for static route:

```yaml
jantao_dev_sitemap:
    ...
    sitemap:
        homepage:
            priority: 0.5
            change_freq: monthly
            last_mod: "2017-02-23T13:14:15+02:00"
```

Full example for dynamic route with parameters:

```yaml
jantao_dev_sitemap:
    ...
    sitemap:
        homepage:
            priority: 0.5
            change_freq: monthly
            last_mod: "2017-02-23T13:14:15+02:00"
            route_parameters:
                slug: "someslug"
```

Iterate data for route parameters with array iterator:

```yaml
jantao_dev_sitemap:
    ...
    sitemap:
        homepage:
            iterator: array
            values:
                - slugone
                - slugtwo
                - slugthree
            priority: 0.5
            change_freq: monthly
            last_mod: "2017-02-23T13:14:15+02:00"
            route_parameters:
                slug: "->"
```

Iterate data for route parameters with array iterator (example 2):

```yaml
jantao_dev_sitemap:
    ...
    sitemap:
        homepage:
            iterator: array
            values:
                - {slug: slugone, change: monthly}
                - {slug: slugtwo, change: hourly}
                - {slug: slugthree, change: dayly}
            priority: 0.5
            change_freq: "->change"
            last_mod: "2017-02-23T13:14:15+02:00"
            route_parameters:
                slug: "->slug"
```

Iterate data for route parameters with Doctrine iterator:

```yaml
jantao_dev_sitemap:
    ...
    sitemap:
        homepage:
            iterator: doctrine
            query: 'SELECT p FROM AppBundle:Page p WHERE p.enabled = true'
            priority: 0.5
            change_freq: monthly
            last_mod: "->modifiedAt"
            route_parameters:
                slug: "->slug"
                categorySlug: "->category->slug"
```

## 3. Execute sitemap generation

There are two ways to execute sitemap generation:

1) via console

```
bin/console jantao_dev:sitemap:generate
```

2) via service

```php
$this->get('jantao_dev.sitemap')->generate();
```

## 4. Advanced configuration

Full bundle configuration:

```yaml
jantao_dev_sitemap:
    hosts:
        - example.com
    gzip: false
    scheme: https
    robots:
        # Robots.txt configuration
    sitemap:
        # Sitemap configuration
```

Parameters description:

| Parameter | Description |
|-----------|-------------|
| hosts     | For one host configuration see chapter 1, For several hosts see chapter 4.2 |
| gzip      | Enable sitemap GZip compression |
| scheme    | Host scheme mode (http, https, https_only*) |
| robots    | See chapter 4.1 |
| sitemap   | See chapters 2.2 and 4.2 |

* https_only means that site is available only over https

### 4.1. Robots.txt configuration

Full robots.txt configuration:

```yaml
jantao_dev_sitemap:
    ...
    robots:
        allow:
            "/ajax/": ~
            "/system/": "Googlebot"
        disallow:
            "/admin/": ~
            "/otherpath": "Googlebot"
        crawl_delay: 5
        clean_param:
            "/photo": ["query=1", "page=2"]
```

Parameters description:

| Parameter   | Description |
|-------------|-------------|
| allow       | Allow entries array, key is a path, value is a user-agent (null means "*") |
| disallow    | Disallow entries array |
| crawl_delay | Crawl delay parameter |
| clean_param | Clean param entries array, key is a path, value is an array of parameters |

For more details about robots.txt parameters see [this](https://yandex.ru/support/webmaster/controlling-robot/robots-txt.xml) or [this](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt).

### 4.2. Several hosts support

Several hosts support allows you to generate different sitemaps to several hosts.

So, enable several hosts support, list your hosts in bundle configuration:

```yaml
jantao_dev_sitemap:
    ...
    hosts:
        example.com
        foo.com
        bar.com
```

Enable robots.txt switching controller in router (add following lines to `app/config/routing.yml`):

```yaml
jantao_dev_sitemap:
    resource: "@JantaoDevSitemapBundle/Resources/config/routing.xml"
    prefix:   /
```

By default all ruotes will be added to each host sitemaps.

But in sitemap section in bundle configuration you can specify host.

Example:

```yaml
jantao_dev_sitemap:
    ...
    hosts:
        example.com
        foo.com
        bar.com
    ...
    sitemap:
        homepage: ~
        about@foo.com: ~
```

In this example homepage will be added to all sitemaps, but about page will be add only to "foo.com" sitemap.

Example 2 (different slugs):

```yaml
jantao_dev_sitemap:
    ...
    hosts:
        example.com
        ru.example.com
        de.example.com
    ...
    sitemap:
        homepage@example.com:
            iterator: doctrine
            query: 'SELECT p FROM AppBundle:Page p WHERE p.enabled = true'
            priority: 0.5
            change_freq: monthly
            last_mod: "->modifiedAt"
            route_parameters:
                slug: "->slugEn"
        homepage@ru.example.com:
            iterator: doctrine
            query: 'SELECT p FROM AppBundle:Page p WHERE p.enabled = true'
            priority: 0.5
            change_freq: monthly
            last_mod: "->modifiedAt"
            route_parameters:
                slug: "->slugRu"
        homepage@de.example.com:
            iterator: doctrine
            query: 'SELECT p FROM AppBundle:Page p WHERE p.enabled = true'
            priority: 0.5
            change_freq: monthly
            last_mod: "->modifiedAt"
            route_parameters:
                slug: "->slugDe"
```

## 5. Add custom event listener

To add a URL using your own logic, you can use event listener or event subscriber.

Example `AppBundle/EventListener/SomeListener.php`:

```php
<?php

namespace AppBundle\EventListener;

use JantaoDev\SitemapBundle\Service\SitemapListenerInterface;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;
use JantaoDev\SitemapBundle\Sitemap\Url;

class SomeListener implements SitemapListenerInterface
{

    public function generateSitemap(SitemapGenerateEvent $event)
    {
        $sitemap = $event->getSitemap();

        $url = new Url('/index.php', new \DateTime('now'), 0.8, 'weekly');
        $sitemap->add($url);
    }
    
}

```

Example `AppBundle/Resources/config/services.yml`:

```yaml
services:
    app.some_listener:
        class: AppBundle\EventListener\SomeListener
        tags:
            - { name: jantao_dev.sitemap.listener }
```

## 6. Notes

TODO:

- cover EventListener classes with tests
- add report on command

## 7. License

This bundle is under MIT license
