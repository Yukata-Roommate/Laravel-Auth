<?php

namespace YukataRm\Laravel\Auth\Commands;

use YukataRm\Laravel\Command\PublishStubsCommand as BaseCommand;

/**
 * Publish Stubs Command
 *
 * @package YukataRm\Laravel\Auth\Commands
 */
class PublishStubsCommand extends BaseCommand
{
    /**
     * command signature
     *
     * @var string
     */
    protected $signature = "auth:publish";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish auth resources";

    /*----------------------------------------*
     * Parameter
     *----------------------------------------*/

    /**
     * set parameter
     *
     * @return void
     */
    protected function setParameter(): void {}

    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * assets name
     *
     * @var string
     */
    protected string $assetsName = "auth";

    /**
     * stubs directory path
     *
     * @var string
     */
    protected string $stubsDirectory = __DIR__ . "/../../stubs";
}
