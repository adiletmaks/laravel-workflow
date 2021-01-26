<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Adiletmaks\LaravelWorkflow\WorkflowServiceProvider;
use Adiletmaks\LaravelWorkflow\Facades\WorkflowFacade;

class BaseWorkflowTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [WorkflowServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Workflow' => WorkflowFacade::class,
        ];
    }
}
