# Twig Translation Extension

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7e09082beab44f75afe2eefdbc55ec50)](https://www.codacy.com/gh/darkalchemy/Twig-Translate/dashboard?utm_source=github.com\&utm_medium=referral\&utm_content=darkalchemy/Twig-Translate\&utm_campaign=Badge_Grade)

A Twig Translation Extension.\
This twig extension has been forked from [Odan/Twig-Translation](https://github.com/odan/twig-translation) because I wanted more flexibility with available methods for translating.\
So, this extension uses [delight-im/PHP-I18N](https://github.com/delight-im/PHP-I18N) to handle the translations.

## Requirements

*   PHP 7.4 or PHP 8.0

## Installation - These instructions assume using php-di for the dependency injection

    composer require darkalchemy/twig-translate

## Integration

### Register the Twig Extension and, the locale codes that will be available

#### This makes the functions available for all strings that are in twig templates

In your container

    \Delight\I18n\I18n::class => DI\factory(function () {
        return new \Delight\I18n\I18n([
            \Delight\I18n\Codes::EN_US,
            \Delight\I18n\Codes::FR_FR,
        ]);
    }),

    \Slim\Views\Twig::class => function (\Psr\Container\ContainerInterface $container) {
        $settings = $container->get(Configuration::class)->all();
        $twig = \Slim\Views\Twig::create($settings['twig']['path'], [
            'cache' => $settings['twig']['cache'] ?? false,
        ]);
        $twig->addExtension(new \darkalchemy\Twig\TwigTranslationExtension($container->get(\Delight\I18n\I18n::class)));

        return $twig;
    }

### Add an $i18n into app.php, so that it can be called by global in helpers.php

    $i18n = $container->get(\Delight\I18n\I18n::class);

### Add the functions, in a file like helpers.php. This makes the functions available for all strings not in twig templates

    function compile_twig_templates(\Psr\Container\ContainerInterface $container)
    {
        $settings    = $container->get(\Selective\Config\Configuration::class)->all();
        $twig_config = $settings['twig'];
        $cache       = $twig_config['cache'] ?? $settings['root'] . '/resources/views/cache/';
        $twig        = $container->get(Twig::class)->getEnvironment();
        $compiler = new \darkalchemy\Twig\TwigCompiler($twig, $cache);

        try {
            $compiler->compile();
        } catch (Exception $e) {
            die($e->getMessage());
        }

        echo "\nCompiling twig templates completed\n\n";
        echo "to fix the permissions, you should run:\nsudo chown -R www-data:www-data {$cache}\nsudo chmod -R 0775 {$cache}\n";

        return 0;
    }

    function __f(string $text, ...$replacements)
    {
        global $i18n;

        return $i18n->translateFormatted($text, ...$replacements);
    }

    function __fe(string $text, ...$replacements)
    {
        global $i18n;

        return $i18n->translateFormattedExtended($text, ...$replacements);
    }

    function __p(string $text, string $alternative, int $count)
    {
        global $i18n;

        return $i18n->translatePlural($text, $alternative, $count);
    }

    function __pf(string $text, string $alternative, int $count, ...$replacements)
    {
        global $i18n;

        return $i18n->translatePluralFormatted($text, $alternative, $count, ...$replacements);
    }

    function __pfe(string $text, string $alternative, int $count, ...$replacements)
    {
        global $i18n;

        return $i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
    }

    function __c(string $text, string $context)
    {
        global $i18n;

        return $i18n->translateWithContext($text, $context);
    }

    function __m(string $text)
    {
        global $i18n;

        return $i18n->markForTranslation($text);
    }

### Copy i18n.sh from I18n to the bin directory

    cp vendor/delight-im/i18n/i18n.sh bin/
    chmod a+x bin/i18n.sh

### In order to translate your twig templates, you first need to compile the templates. Create a file bin/translate.php and add this to it

    <?php

    declare(strict_types=1);

    use Delight\I18n\I18n;
    use Selective\Config\Configuration;

    $container = (require_once __DIR__ . '/../bootstrap/app.php')->getContainer();

    $root_path = $container->get(Configuration::class)->findString('root');
    $processes = [
        'compile',
        'translate',
    ];

    $languages = [];
    $locales   = $container->get(I18n::class)->getSupportedLocales();
    foreach ($locales as $locale) {
        $languages[] = str_replace('-', '_', $locale);
    }
    $process = 'compile';
    $lang    = 'en_US';
    foreach ($argv as $arg) {
        if (in_array($arg, $processes)) {
            $process = $arg;
        } elseif (in_array($arg, $languages)) {
            $lang = $arg;
        }
    }

    switch ($process) {
        case 'translate':
            copy($root_path . '/bin/i18n.sh', $root_path . '/i18n.sh');
            chmod($root_path . '/i18n.sh', 0755);
            passthru("sed -i 's/\\-\\-keyword \\-\\-keyword/\\-\\-keyword \\-\\-keyword=\"translateFormatted:1\" \\-\\-keyword=\"translateFormattedExtended:1\" \\-\\-keyword=\"translatePlural:1,2,3t\" \\-\\-keyword=\"translatePluralFormatted:1,2\" \\-\\-keyword=\"translatePluralFormattedExtended:1,2\" \\-\\-keyword=\"translateWithContext:1,2c,2t\" \\-\\-keyword=\"markForTranslation:1,1t\" \\-\\-flag=\"translateFormatted:1:php\\-format\" \\-\\-flag=\"translateFormattedExtended:1:no\\-php\\-format\" \\-\\-flag=\"translatePluralFormatted:1:php\\-format\" \\-\\-flag=\"translatePluralFormattedExtended:1:no\\-php\\-format\" \\-\\-keyword/g' i18n.sh");
            passthru(sprintf('./i18n.sh %s', $lang));
            unlink($root_path . '/i18n.sh');

            break;
        default:
            compile_twig_templates($container);

            break;
    }

### To translate into fr\_FR locale, run

    php bin/translate.php fr_FR

Then, edit the messages.po in poedit, validate and save.

For full documentation on how to use each of the functions, please refer to [PHP-I18n](https://github.com/delight-im/PHP-I18N) where each function is well documented.
