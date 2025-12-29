<?php

namespace Haida\FilamentWorkhub;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentWorkhub\Support\WorkhubCapabilities;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Haida\FilamentWorkhub\Support\AutomationRegistry;
use Haida\FilamentWorkhub\Support\WorkhubAutomationDefaults;
use Haida\FilamentWorkhub\Services\WorkhubAutomationEngine;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Models\Attachment;
use Haida\FilamentWorkhub\Models\AuditEvent;
use Haida\FilamentWorkhub\Models\AutomationRule;
use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\Decision;
use Haida\FilamentWorkhub\Models\EntityReference;
use Haida\FilamentWorkhub\Models\Label;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\TimeEntry;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\Watcher;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Models\WorkType;
use Haida\FilamentWorkhub\Models\Workflow;
use Haida\FilamentWorkhub\Observers\AttachmentObserver;
use Haida\FilamentWorkhub\Observers\CommentObserver;
use Haida\FilamentWorkhub\Observers\DecisionObserver;
use Haida\FilamentWorkhub\Observers\ProjectObserver;
use Haida\FilamentWorkhub\Observers\TimeEntryObserver;
use Haida\FilamentWorkhub\Observers\WatcherObserver;
use Haida\FilamentWorkhub\Observers\WorkItemObserver;
use Haida\FilamentWorkhub\Policies\AttachmentPolicy;
use Haida\FilamentWorkhub\Policies\AuditEventPolicy;
use Haida\FilamentWorkhub\Policies\AutomationRulePolicy;
use Haida\FilamentWorkhub\Policies\CommentPolicy;
use Haida\FilamentWorkhub\Policies\CustomFieldPolicy;
use Haida\FilamentWorkhub\Policies\DecisionPolicy;
use Haida\FilamentWorkhub\Policies\EntityReferencePolicy;
use Haida\FilamentWorkhub\Policies\LabelPolicy;
use Haida\FilamentWorkhub\Policies\ProjectPolicy;
use Haida\FilamentWorkhub\Policies\StatusPolicy;
use Haida\FilamentWorkhub\Policies\TimeEntryPolicy;
use Haida\FilamentWorkhub\Policies\TransitionPolicy;
use Haida\FilamentWorkhub\Policies\WatcherPolicy;
use Haida\FilamentWorkhub\Policies\WorkItemPolicy;
use Haida\FilamentWorkhub\Policies\WorkTypePolicy;
use Haida\FilamentWorkhub\Policies\WorkflowPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Haida\FilamentWorkhub\Listeners\WorkhubEventSubscriber;
use Haida\FilamentWorkhub\Console\Commands\RunWorkhubAutomation;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentWorkhubServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-workhub')
            ->hasConfigFile('filament-workhub')
            ->hasViews()
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_27_010000_create_workhub_workflows_table',
                '2025_12_27_010001_create_workhub_statuses_table',
                '2025_12_27_010002_create_workhub_transitions_table',
                '2025_12_27_010003_create_workhub_work_types_table',
                '2025_12_27_010004_create_workhub_projects_table',
                '2025_12_27_010005_create_workhub_work_items_table',
                '2025_12_27_010006_create_workhub_labels_table',
                '2025_12_27_010007_create_workhub_label_work_item_table',
                '2025_12_27_010008_create_workhub_comments_table',
                '2025_12_27_010009_create_workhub_attachments_table',
                '2025_12_27_010010_create_workhub_watchers_table',
                '2025_12_27_010011_create_workhub_time_entries_table',
                '2025_12_27_010012_create_workhub_decisions_table',
                '2025_12_27_010013_create_workhub_audit_events_table',
                '2025_12_27_010014_create_workhub_entity_references_table',
                '2025_12_27_010015_create_workhub_automation_rules_table',
                '2025_12_27_010016_create_workhub_custom_fields_table',
                '2025_12_27_010017_create_workhub_custom_field_values_table',
                '2025_12_27_010018_add_last_ran_at_to_workhub_automation_rules_table',
                '2025_12_27_010019_add_allowed_link_types_to_workhub_projects_table',
            ])
            ->hasCommands([
                RunWorkhubAutomation::class,
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(EntityReferenceRegistry::class);
        $this->app->singleton(AutomationRegistry::class);
        $this->app->singleton(WorkhubAutomationEngine::class);
        $this->app->singleton(\Haida\FilamentWorkhub\Services\WorkhubWebhookDispatcher::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(WorkItem::class, WorkItemPolicy::class);
        Gate::policy(Workflow::class, WorkflowPolicy::class);
        Gate::policy(Status::class, StatusPolicy::class);
        Gate::policy(Transition::class, TransitionPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Attachment::class, AttachmentPolicy::class);
        Gate::policy(Watcher::class, WatcherPolicy::class);
        Gate::policy(TimeEntry::class, TimeEntryPolicy::class);
        Gate::policy(Decision::class, DecisionPolicy::class);
        Gate::policy(AuditEvent::class, AuditEventPolicy::class);
        Gate::policy(Label::class, LabelPolicy::class);
        Gate::policy(EntityReference::class, EntityReferencePolicy::class);
        Gate::policy(WorkType::class, WorkTypePolicy::class);
        Gate::policy(CustomField::class, CustomFieldPolicy::class);
        Gate::policy(AutomationRule::class, AutomationRulePolicy::class);

        Project::observe(ProjectObserver::class);
        WorkItem::observe(WorkItemObserver::class);
        Comment::observe(CommentObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Watcher::observe(WatcherObserver::class);
        TimeEntry::observe(TimeEntryObserver::class);
        Decision::observe(DecisionObserver::class);

        Event::subscribe(WorkhubEventSubscriber::class);

        $registry = $this->app->make(CapabilityRegistryInterface::class);
        WorkhubCapabilities::register($registry);

        $linkRegistry = $this->app->make(EntityReferenceRegistry::class);
        $linkRegistry->register(
            'workhub.project',
            Project::class,
            'پروژه',
            'heroicon-o-rectangle-stack',
            fn (Project $project) => ProjectResource::getUrl('edit', ['record' => $project]),
            fn (Project $project) => $project->key.' - '.$project->name,
        );
        $linkRegistry->register(
            'workhub.work_item',
            WorkItem::class,
            'آیتم کاری',
            'heroicon-o-clipboard-document-list',
            fn (WorkItem $item) => WorkItemResource::getUrl('view', ['record' => $item]),
            fn (WorkItem $item) => $item->key.' - '.$item->title,
        );

        $automationRegistry = $this->app->make(AutomationRegistry::class);
        $automationRegistry->registerDefaults([WorkhubAutomationDefaults::class, 'register']);
    }
}
