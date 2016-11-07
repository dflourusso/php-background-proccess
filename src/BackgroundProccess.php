<?php
namespace Dflourusso;

/**
 * Class BackgroundProccess
 * @package Dflourusso
 */
class BackgroundProccess
{

    /**
     * @var string
     */
    protected $name;

    /**
     * Path to store the command output and PID
     *
     * @var string
     */
    protected $cache_directory;

    /**
     * @param string $name
     * @param string $cache_directory
     */
    public function __construct($name, $cache_directory = 'tmp/php-background-proccess')
    {
        $this->name            = $name;
        $this->cache_directory = $cache_directory;
        $this->ensureUnixOs();
        $this->createCacheDirectory();
    }

    /**
     * Run the command
     *
     * @param string $command
     * @param bool   $throw
     *
     * @return bool
     * @throws \Exception
     */
    public function run($command, $throw = false)
    {
        if ($this->isRunning()) {
            if ($throw) {
                throw new \Exception('There is already a running background process with this name.');
            } else {
                return false;
            }
        } else {
            exec(sprintf("%s > %s 2>&1 & echo $! > %s", $command, $this->getOutputFileName(), $this->getPidFileName()));

            return true;
        }
    }

    /**
     * Kill the background proccess
     */
    public function stop()
    {
        if ($this->isRunning()) {
            exec(sprintf("ps %d", $this->getPid()));
        }
    }

    /**
     * Check if the proccess is running
     *
     * @return bool
     */
    public function isRunning()
    {
        try {
            $result = shell_exec(sprintf("ps %d", $this->getPid()));
            if (count(preg_split("/\n/", $result)) > 2) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Returns the proccess PID
     *
     * @return null|int
     */
    public function getPid()
    {
        if (file_exists($this->getPidFileName())) {
            return (int)file_get_contents($this->getPidFileName());
        }

        return null;
    }

    /**
     * Thows an exception if server os is not unix
     *
     * @throws \Exception
     */
    protected function ensureUnixOs()
    {
        if (!in_array(strtoupper(PHP_OS), ['LINUX', 'FREEBSD', 'DARWIN'])) {
            throw new \Exception('OS must be unix.');
        }
    }

    /**
     * Create the cache directory if it does not exist
     *
     * @throws \Exception
     */
    protected function createCacheDirectory()
    {
        if (!file_exists($this->cache_directory)) {
            mkdir($this->cache_directory);
        }
    }

    /**
     * Returns the PID store file name
     *
     * @return string
     */
    protected function getPidFileName()
    {
        return "{$this->cache_directory}/{$this->name}.pid";
    }

    /**
     * Returns the output store file name
     *
     * @return string
     */
    protected function getOutputFileName()
    {
        return "{$this->cache_directory}/{$this->name}.out";
    }

}
