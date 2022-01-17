<?php

declare(strict_types=1);

namespace Darkalchemy\Twig;

use Delight\I18n\I18n;
use Delight\I18n\Throwable\LocaleNotSupportedException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigTranslationExtension.
 */
class TwigTranslationExtension extends AbstractExtension
{
    protected I18n $i18n;
    protected array $locales;

    /**
     * TwigTranslationExtension constructor.
     *
     * @param I18n             $i18n       The i18n
     */
    public function __construct(I18n $i18n)
    {
        $this->i18n       = $i18n;
        $this->locales    = $this->i18n->getSupportedLocales();

        $this->setUserLocale();
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('_f', [$this, 'translateFormatted']),
            new TwigFunction('_fe', [$this, 'translateFormattedExtended']),
            new TwigFunction('_p', [$this, 'translatePlural']),
            new TwigFunction('_pf', [$this, 'translatePluralFormatted']),
            new TwigFunction('_pfe', [$this, 'translatePluralFormattedExtended']),
            new TwigFunction('_c', [$this, 'translateWithContext']),
            new TwigFunction('locale', [$this, 'locale']),
            new TwigFunction('getUserLocale', [$this, 'getUserLocale']),
            new TwigFunction('nativeLanguageName', [$this, 'nativeLanguageName']),
            new TwigFunction('supportedLocales', [$this, 'supportedLocales']),
        ];
    }

    /**
     * @param string $text The text
     *
     * @return string
     */
    public function translateFormatted(string $text): string
    {
        return $this->i18n->translateFormatted($text);
    }

    /**
     * @param string $text            The text
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function translateFormattedExtended(string $text, ...$replacements): string
    {
        return $this->i18n->translateFormattedExtended($text, ...$replacements);
    }

    /**
     * @param string $text            The text
     * @param string $alternative     The alternative
     * @param int    $count           The count
     * @param mixed  ...$replacements The replacements
     *
     * @return string
     */
    public function translatePluralFormatted(string $text, string $alternative, int $count, ...$replacements): string
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
    public function translatePluralFormattedExtended(
        string $text,
        string $alternative,
        int $count,
        ...$replacements
    ): string {
        return $this->i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param string $text    The text
     * @param string $context The context
     *
     * @return string
     */
    public function translateWithContext(string $text, string $context): string
    {
        return $this->i18n->translateWithContext($text, $context);
    }

    /**
     * @return array
     */
    public function supportedLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return string
     */
    public function getUserLocale(): string
    {
        return $this->i18n->getLocale();
    }

    /**
     * setUserLocal.
     */
    public function setUserLocale(): void
    {
        try {
            $this->i18n->setLocaleManually($this->getUserLocale());
        } catch (LocaleNotSupportedException $e) {
            die($e->getMessage());
        }
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
