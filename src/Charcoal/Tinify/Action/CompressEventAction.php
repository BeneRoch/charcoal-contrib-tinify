<?php

namespace Charcoal\Tinify\Action;

// from charcoal-app
use Charcoal\App\Action\AbstractAction;

// from psr-7
use Charcoal\Tinify\TinifyServiceTrait;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Optimize Action
 */
class CompressEventAction extends AbstractAction
{
    use TinifyServiceTrait;

    /**
     * Gets a psr7 request and response and returns a response.
     *
     * Called from `__invoke()` as the first thing.
     *
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        $this->setMode(self::MODE_EVENT_STREAM);

        return $response;
    }

    /**
     * Give an opportunity to children classes to inject dependencies from a Pimple Container.
     *
     * Does nothing by default, reimplement in children classes.
     *
     * The `$container` DI-container (from `Pimple`) should not be saved or passed around, only to be used to
     * inject dependencies (typically via setters).
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setTinifyService($container['tinify']);
    }

    /**
     * @return \Generator
     */
    private function optimizeFiles()
    {
        for ($i = 1; $i <= 10; $i++) {
            yield [
                'id'       => 'test_'.$i,
                'text'     => sprintf('%s / %s', $i, 10),
                'progress' => floor(100 / 10 * $i)
            ];
            sleep(1);
        }
    }

    /**
     * Returns an associative array of results (set after being  invoked / run).
     *
     * The raw array of results will be called from `__invoke()`.
     *
     * @return array|mixed
     */
    public function results()
    {
        foreach ($this->optimizeFiles() as $file) {
            echo 'data: '.json_encode($file).PHP_EOL.PHP_EOL;

            ob_flush();
            flush();
        }

        echo 'event: CLOSE'.PHP_EOL;
        echo 'data: '.PHP_EOL.PHP_EOL;

        ob_flush();
        flush();

        return [];
    }
}
