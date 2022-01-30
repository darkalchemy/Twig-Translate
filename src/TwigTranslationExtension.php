<?php

declare(strict_types=1);

namespace Darkalchemy\Twig;

use Delight\I18n\I18n;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigTranslationExtension.
 */
class TwigTranslationExtension extends AbstractExtension
{
    /**
     * @var I18n
     */
    protected I18n $i18n;

    /**
     * TwigTranslationExtension constructor.
     *
     * @param I18n $i18n The i18n
     */
    public function __construct(I18n $i18n)
    {
        $this->i18n = $i18n;
    }

    /**
     * @param string $text            The text
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function __f(string $text, ...$replacements): string
    {
        return $this->i18n->translateFormatted($text, ...$replacements);
    }

    /**
     * @param string $text            The text
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function __fe(string $text, ...$replacements): string
    {
        return $this->i18n->translateFormattedExtended($text, ...$replacements);
    }

    /**
     * @param string $text        The text
     * @param string $alternative The alternative
     * @param int    $count       The count
     *
     * @return string
     */
    public function __p(string $text, string $alternative, int $count): string
    {
        return $this->i18n->translatePlural($text, $alternative, $count);
    }

    /**
     * @param string $text            The text
     * @param string $alternative     The alternative
     * @param int    $count           The count
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function __pf(string $text, string $alternative, int $count, ...$replacements): string
    {
        return $this->i18n->translatePluralFormatted($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param string $text            The text
     * @param string $alternative     The alternative
     * @param int    $count           The count
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function __pfe(string $text, string $alternative, int $count, ...$replacements): string
    {
        return $this->i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param string $text    The text
     * @param string $context The context
     *
     * @return string
     */
    public function __c(string $text, string $context): string
    {
        return $this->i18n->translateWithContext($text, $context);
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('__f', [$this, '__f']),
            new TwigFunction('__fe', [$this, '__fe']),
            new TwigFunction('__p', [$this, '__p']),
            new TwigFunction('__pf', [$this, '__pf']),
            new TwigFunction('__pfe', [$this, '__pfe']),
            new TwigFunction('__c', [$this, '__c']),
            new TwigFunction('getUserLocale', [$this, 'getUserLocale']),
            new TwigFunction('nativeLanguageName', [$this, 'nativeLanguageName']),
            new TwigFunction('supportedLocales', [$this, 'supportedLocales']),
        ];
    }

    /**
     * @return array
     */
    public function supportedLocales(): array
    {
        return $this->i18n->getSupportedLocales();
    }

    /**
     * @return string
     */
    public function getUserLocale(): string
    {
        $session = new SimpleSession();

        return $this->i18n->getLocale() ??
            $session->getSessionValue($this->i18n->getSessionField() ?? 'locale') ??
            $this->supportedLocales()[0];
    }

    /**
     * @param string $locale The locale to use
     *
     * @return null|string
     */
    public function nativeLanguageName(string $locale): ?string
    {
        return $this->i18n->getNativeLanguageName($locale);
    }
}
