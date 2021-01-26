<?php

namespace Tests;

use Illuminate\Support\Facades\Event;
use Adiletmaks\LaravelWorkflow\Events\AnnounceEvent;
use Adiletmaks\LaravelWorkflow\Events\CompletedEvent;
use Adiletmaks\LaravelWorkflow\Events\EnteredEvent;
use Adiletmaks\LaravelWorkflow\Events\EnterEvent;
use Adiletmaks\LaravelWorkflow\Events\GuardEvent;
use Adiletmaks\LaravelWorkflow\Events\LeaveEvent;
use Adiletmaks\LaravelWorkflow\Events\TransitionEvent;
use Adiletmaks\LaravelWorkflow\WorkflowRegistry;
use Tests\Fixtures\TestObject;

class WorkflowSubscriberTest extends BaseWorkflowTestCase
{
    public function testIfWorkflowEmitsEvents()
    {
        Event::fake();

        $config = [
            'straight' => [
                'supports'    => [TestObject::class],
                'places'      => ['a', 'b', 'c'],
                'transitions' => [
                    't1' => [
                        'from' => 'a',
                        'to'   => 'b',
                    ],
                    't2' => [
                        'from' => 'b',
                        'to'   => 'c',
                    ],
                ],
            ],
        ];

        $registry = new WorkflowRegistry($config);
        $object = new TestObject();
        $workflow = $registry->get($object);

        $workflow->apply($object, 't1');

        // Symfony Workflow 4.2.9 fires entered event on initialize
        Event::assertDispatched(EnteredEvent::class);
        Event::assertDispatched('workflow.entered');
        Event::assertDispatched('workflow.straight.entered');

        Event::assertDispatched(GuardEvent::class);
        Event::assertDispatched('workflow.guard');
        Event::assertDispatched('workflow.straight.guard');
        Event::assertDispatched('workflow.straight.guard.t1');

        Event::assertDispatched(LeaveEvent::class);
        Event::assertDispatched('workflow.leave');
        Event::assertDispatched('workflow.straight.leave');
        Event::assertDispatched('workflow.straight.leave.a');

        Event::assertDispatched(TransitionEvent::class);
        Event::assertDispatched('workflow.transition');
        Event::assertDispatched('workflow.straight.transition');
        Event::assertDispatched('workflow.straight.transition.t1');

        Event::assertDispatched(EnterEvent::class);
        Event::assertDispatched('workflow.enter');
        Event::assertDispatched('workflow.straight.enter');
        Event::assertDispatched('workflow.straight.enter.b');

        Event::assertDispatched(EnteredEvent::class);
        Event::assertDispatched('workflow.entered');
        Event::assertDispatched('workflow.straight.entered');
        Event::assertDispatched('workflow.straight.entered.b');

        Event::assertDispatched(CompletedEvent::class);
        Event::assertDispatched('workflow.completed');
        Event::assertDispatched('workflow.straight.completed');
        Event::assertDispatched('workflow.straight.completed.t1');

        Event::assertDispatched(GuardEvent::class);
        Event::assertDispatched('workflow.guard');
        Event::assertDispatched('workflow.straight.guard');
        Event::assertDispatched('workflow.straight.guard.t2');
    }
}
