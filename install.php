<?php
/**
 * Step 1: Clone this repository to your computer
 * Step 2: Run installation script
 */

/**
 * Parameters of project
 */
$parameters = [
    'PROJECT'     => 'lemonphp/bee',
    'PACKAGE'     => 'lemonphp/bee',
    'NAMESPACE'   => 'Lemon\Bee',
    'DESCRIPTION' => 'Bee project og LemonPHP Team',
];

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
     * Compiler string
     *
     * @param array $matches
     * @return string
     */
    protected function compiler(array $matches)
    {
        if (isset($matches[1]) && isset($this->parameters[$matches[1]])) {
            return quotemeta($this->parameters[$matches[1]]);
        }
        return $matches[0];
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

            if (!is_file($sourceFile) || !is_readable($sourceFile) || false === ($content = file_get_contents($sourceFile))) {
                continue;
            }
            $newContent = preg_replace_callback('/\{\{([A-Z]+)\}\}/', [$this, 'compiler'], $content);
            if (false === file_put_contents($sourceFile, $newContent)) {
                echo "Write to file '{$sourceFile}' failure.\n";
                exit(1);
            }
        }

        foreach ($this->unusedFiles as $file) {
            $sourceFile = __DIR__ . DIRECTORY_SEPARATOR . $file;
            if (!is_file($sourceFile)) {
                continue;
            }

            if (is_dir($sourceFile)) {
                $deleted = rmdir($sourceFile);
            } else {
                $deleted = unlink($sourceFile);
            }

            if (!$deleted) {
                echo "Delete file '{$sourceFile}' failure.\n";
                exit(1);
            }
        }

        exit(0);
    }
}

/**
 * Installing
 */
$cmd = new Installation($parameters);
$cmd->run();
