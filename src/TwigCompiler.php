<?php

declare(strict_types=1);

namespace Darkalchemy\Twig;

use Exception;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Yiisoft\Files\FileHelper;

/**
 * Class TwigCompiler.
 */
class TwigCompiler
{
    protected Environment $twig;
    protected TwigTranslationExtension $ext;
    protected string $cachePath;
    protected bool $verbose;

    /**
     * The TwigCompiler constructor.
     *
     * @param Environment $twig      The Twig Environment instance
     * @param string      $cachePath The twig cache path
     * @param bool        $verbose   Enable verbose output
     */
    public function __construct(Environment $twig, TwigTranslationExtension $ext, string $cachePath, bool $verbose = false)
    {
        $this->ext = $ext;
        if (empty($cachePath)) {
            throw new InvalidArgumentException($ext->_f('The cache path is required'));
        }

        $this->twig      = $twig;
        $this->cachePath = str_replace('\\', '/', $cachePath);
        $this->verbose   = $verbose;
    }

    /**
     * Compile all twig templates.
     *
     * @throws Exception Exception
     *
     * @return bool Success
     */
    public function compile(): bool
    {
        $files = new FileHelper();
        $files->ensureDirectory($this->cachePath, 0777);
        $files->clearDirectory($this->cachePath);

        // Iterate over all your templates and force compilation
        $this->twig->disableDebug();
        $this->twig->enableAutoReload();

        // The Twig cache must be enabled
        if (!$this->twig->getCache()) {
            $this->twig->setCache($this->cachePath);
        }

        $loader = $this->twig->getLoader();

        if ($loader instanceof FilesystemLoader) {
            $paths = $loader->getPaths();

            foreach ($paths as $path) {
                $this->compileFiles($path);
            }
        }

        return true;
    }

    /**
     * @param string $viewPath The view path
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function compileFiles(string $viewPath): void
    {
        $directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'twig') {
                continue;
            }

            $templateName = substr($file->getPathname(), strlen($viewPath) + 1);
            $templateName = str_replace('\\', '/', $templateName);

            if ($this->verbose) {
                echo sprintf("Parsing: %s\n", $templateName);
            }

            $this->twig->load($templateName);
        }
    }
}
