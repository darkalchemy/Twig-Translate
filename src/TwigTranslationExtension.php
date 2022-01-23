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
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('_f', [$this, '_f']),
            new TwigFunction('_fe', [$this, '_fe']),
            new TwigFunction('_p', [$this, '_p']),
            new TwigFunction('_pf', [$this, '_pf']),
            new TwigFunction('_pfe', [$this, '_pfe']),
            new TwigFunction('_c', [$this, '_c']),
            new TwigFunction('getUserLocale', [$this, 'getUserLocale']),
            new TwigFunction('nativeLanguageName', [$this, 'nativeLanguageName']),
            new TwigFunction('supportedLocales', [$this, 'supportedLocales']),
        ];
    }

    /**
     * @param string $text            The text
     * @param mixed  ...$replacements
     *
     * @return string
     */
    public function _f(string $text, ...$replacements): string
    {
        return $this->i18n->_f($text, ...$replacements);
    }

    /**
     * @param string $text            The text
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function _fe(string $text, ...$replacements): string
    {
        return $this->i18n->_fe($text, ...$replacements);
    }

    /**
     * @param string $text        The text
     * @param string $alternative The alternative
     * @param int    $count       The count
     *
     * @return string
     */
    public function _p(string $text, string $alternative, int $count): string
    {
        return $this->i18n->_p($text, $alternative, $count);
    }

    /**
     * @param string $text            The text
     * @param string $alternative     The alternative
     * @param int    $count           The count
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function _pf(string $text, string $alternative, int $count, ...$replacements): string
    {
        return $this->i18n->_pf($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param string $text            The text
     * @param string $alternative     The alternative
     * @param int    $count           The count
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function _pfe(string $text, string $alternative, int $count, ...$replacements): string
    {
        return $this->i18n->_pfe($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param string $text    The text
     * @param string $context The context
     *
     * @return string
     */
    public function _c(string $text, string $context): string
    {
        return $this->i18n->_c($text, $context);
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
        return $this->i18n->getLocale() ?? $_SESSION[$this->i18n->getSessionField() ?? 'locale'] ?? $this->supportedLocales()[0];
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
