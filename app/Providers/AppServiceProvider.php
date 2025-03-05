<?php

namespace App\Providers;

use App\Interfaces\BookCopyRepositoryInterface;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\BorrowingServiceInterface;
use App\Interfaces\BranchBookTransferRepositoryInterface;
use App\Interfaces\EventStoreInterface;
use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\QueueServiceInterface;
use App\Interfaces\ReservationServiceInterface;
use App\Interfaces\ScoreServiceInterface;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Policies\BorrowingPolicy;
use App\Policies\ReservationPolicy;
use App\Repositories\BookRepository;
use App\Repositories\BranchBookTransferRepository;
use App\Services\BorrowingService;
use App\Services\EventStore;
use App\Services\NotificationService;
use App\Services\QueueService;
use App\Services\ReservationService;
use App\Services\ScoreService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(BookCopyRepositoryInterface::class, BookCopyRepositoryInterface::class);
        $this->app->bind(ReservationServiceInterface::class, ReservationService::class);
        $this->app->bind(BorrowingServiceInterface::class, BorrowingService::class);
        $this->app->bind(ScoreServiceInterface::class, ScoreService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        $this->app->bind(EventStoreInterface::class, EventStore::class);
        $this->app->bind(QueueServiceInterface::class, QueueService::class);
        $this->app->bind(BranchBookTransferRepositoryInterface::class, BranchBookTransferRepository::class);



    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Reservation::class, ReservationPolicy::class);
        Gate::policy(Borrowing::class, BorrowingPolicy::class);
    }
}
