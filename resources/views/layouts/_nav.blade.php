<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Threads
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="/threads/create">New Thread</a>
                        <a class="dropdown-item" href="/threads">All Threads</a>
                        @auth
                        <a class="dropdown-item" href="/threads?by={{ auth()->user()->username }}">My Threads</a>
                        @endauth
                        <a class="dropdown-item" href="/threads?popular=all">Popular All Time</a>
                        <a class="dropdown-item" href="/threads?unanswered=all">Unanswered Threads</a>
                    </div>
                </li>
                <channel-dropdown></channel-dropdown>
                <a class="nav-link" role="button" data-toggle="modal" data-target="#exampleModal">
                    Leaderboard
                </a>
                <leaderboard></leaderboard>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">

                <user-notifications></user-notifications>

                <!-- Authentication Links -->
                @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else

                @if (Auth::user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
                        <span class="fas fa-cog" aria-hidden="true"></span>
                    </a>
                </li>
                @endif

                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('profiles.show', Auth::user()->username) }}">
                            Profile
                        </a>

                        <logout-button route="{{ route('logout') }}">{{ __('Logout') }}</logout-button>
                    </div>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
