<?php
/**
 * Step 1: Clone this repository
 * Step 2: Modify project's parameters in line 157 of this script
 * Step 3: Run this script
 */

/**
 * Class Installtion
 */
class Installation
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $textFiles = [
        '.gitattributes',
        '.gitignore',
        'CHANGELOG.md',
        'LICENSE.md',
        'README.md',
        'composer.json',
        'phpcs.xml.dist',
        'phpunit.xml.dist',
        'tests/bootstrap.php'
    ];

    /**
     * @var array
     */
    protected $unusedFiles = [
        'bin/empty',
        'src/empty',
        '.git',
        'install.php',
    ];

    /**
     * A string compiler
     *
     * @param array $matches
     * @return string
     */
    protected function compiler(array $matches)
    {
        if (isset($matches[1]) && isset($this->parameters[$matches[1]])) {
            return json_encode($this->parameters[$matches[1]]);
        }
        return $matches[0];
    }

    /**
     * Compile a file
     *
     * @param string $file
     * @return boolean
     */
    protected function compileFile($file)
    {
        if (false === ($content = file_get_contents($file))) {
            return false;
        }
        $count      = 0;
        $newContent = preg_replace_callback('/\{\{([A-Z]+)\}\}/', [$this, 'compiler'], $content, -1, $count);
        if ($count > 0 && false === file_put_contents($file, $newContent)) {
            return false;
        }

        return true;
    }

    /**
     * Remove directory recursively
     *
     * @param string $dir
     * @return boolean
     */
    protected function rmdir($dir)
    {
        try {
            $iterator = new \DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isDot()) {
                    continue;
                }
                if ($fileinfo->isDir() && !$this->rmdir($fileinfo->getPathname())) {
                    return false;
                }
                if ($fileinfo->isFile() && !unlink($fileinfo->getPathname())) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return rmdir($dir);
    }

    /**
     * Constructor
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Run installation
     */
    public function run()
    {
        foreach ($this->textFiles as $file) {
            $sourceFile = __DIR__ . DIRECTORY_SEPARATOR . $file;

            if (!is_file($sourceFile) || !is_readable($sourceFile)) {
                continue;
            }
            if (!$this->compileFile($sourceFile)) {
                echo "Write to file '{$sourceFile}' failure.\n";
                exit(1);
            }
        }

        foreach ($this->unusedFiles as $file) {
            $sourceFile = __DIR__ . DIRECTORY_SEPARATOR . $file;

            if (is_dir($sourceFile)) {
                $deleted = $this->rmdir($sourceFile);
            } elseif (is_file($sourceFile)) {
                $deleted = unlink($sourceFile);
            } else {
                $deleted = true;
            }

            if (!$deleted) {
                echo "Delete file '{$sourceFile}' failure.\n";
                exit(1);
            }
        }

        echo 'Done';
        exit(0);
    }
}

/**
 * Parameters of project
 * Please change this variable
 */
$parameters = [
    'PROJECT'     => 'lemonphp/bee',
    'TYPE'        => 'library',
    'PACKAGE'     => 'lemonphp/bee',
    'NAMESPACE'   => 'Lemon\Bee',
    'DESCRIPTION' => 'A project of LemonPHP Team, it named Bee',
    'KEYWORDS'    => ['demo', 'bee'],
];

/**
 * Installing
 */
$cmd = new Installation($parameters);
$cmd->run();
