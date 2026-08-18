<?php

use App\Models\Link;
use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    #[Computed]
    public function totalUsers(): int
    {
        return User::count();
    }

    #[Computed]
    public function suspendedUsers(): int
    {
        return User::where('is_active', false)->count();
    }

    #[Computed]
    public function totalLinks(): int
    {
        return Link::count();
    }

    #[Computed]
    public function totalSocialLinks(): int
    {
        return SocialLink::count();
    }

    #[Computed]
    public function newUsersLast7Days(): int
    {
        return User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    #[Computed]
    public function topViewedUsers()
    {
        return User::query()
            ->where('profile_views', '>', 0)
            ->orderByDesc('profile_views')
            ->limit(5)
            ->get(['id', 'name', 'username', 'profile_views']);
    }

    /**
     * @return array<int, array{date: string, count: int}>
     */
    #[Computed]
    public function signupsByDay(): array
    {
        $counts = User::query()
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->get(['created_at'])
            ->groupBy(fn (User $user) => $user->created_at->format('Y-m-d'))
            ->map->count();

        return collect(range(6, 0))
            ->map(function (int $daysAgo) use ($counts) {
                $date = Carbon::now()->subDays($daysAgo)->format('Y-m-d');

                return ['date' => $date, 'count' => $counts->get($date, 0)];
            })
            ->all();
    }
}; ?>

<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <p class="text-sm text-gray-500">{{ __('總使用者數') }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $this->totalUsers }}</p>
        </div>

        <div class="bg-white p-6 shadow sm:rounded-lg">
            <p class="text-sm text-gray-500">{{ __('近 7 日新註冊') }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $this->newUsersLast7Days }}</p>
        </div>

        <div class="bg-white p-6 shadow sm:rounded-lg">
            <p class="text-sm text-gray-500">{{ __('已停權帳號') }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $this->suspendedUsers }}</p>
        </div>

        <div class="bg-white p-6 shadow sm:rounded-lg">
            <p class="text-sm text-gray-500">{{ __('連結總數') }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $this->totalLinks }}</p>
        </div>

        <div class="bg-white p-6 shadow sm:rounded-lg">
            <p class="text-sm text-gray-500">{{ __('社群連結總數') }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $this->totalSocialLinks }}</p>
        </div>
    </div>

    <div class="bg-white p-6 shadow sm:rounded-lg">
        <p class="mb-4 text-sm font-medium text-gray-700">{{ __('瀏覽數最高的個人頁面') }}</p>

        @if ($this->topViewedUsers->isEmpty())
            <p class="text-sm text-gray-500">{{ __('目前尚無瀏覽紀錄。') }}</p>
        @else
            <ol class="divide-y divide-gray-100">
                @foreach ($this->topViewedUsers as $index => $user)
                    <li class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-3">
                            <span class="w-5 text-sm font-medium text-gray-400">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">&commat;{{ $user->username }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ number_format($user->profile_views) }} {{ __('次瀏覽') }}</span>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    <div class="bg-white p-6 shadow sm:rounded-lg">
        <p class="mb-4 text-sm font-medium text-gray-700">{{ __('近 7 日註冊趨勢') }}</p>

        <div class="flex items-end gap-3" style="height: 6rem;">
            @php
                $maxCount = max(1, collect($this->signupsByDay)->max('count'));
            @endphp

            @foreach ($this->signupsByDay as $day)
                <div class="flex flex-1 flex-col items-center gap-1">
                    <div
                        class="w-full rounded-t bg-indigo-400"
                        style="height: {{ $day['count'] > 0 ? max(4, intdiv($day['count'] * 80, $maxCount)) : 2 }}px;"
                        title="{{ $day['date'] }}：{{ $day['count'] }}"
                    ></div>
                    <span class="text-xs text-gray-400">{{ substr($day['date'], 5) }}</span>
                    <span class="text-xs font-medium text-gray-600">{{ $day['count'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
