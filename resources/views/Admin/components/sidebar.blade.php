<aside id="sidebar" class="sidebar py-5 px-6">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center {{request()->is('/') ? '' : 'collapsed'}}" href="{{ url('/') }}">
                <i class="bi bi-grid"></i>
                <span style="margin-top:-3px;">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center collapsed" data-bs-target="#tasksSlideDown" data-bs-toggle="collapse" href="#">
                <i class="bi bi-list-task"></i><span>Tasks</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tasksSlideDown" class="nav-content collapse {{request()->is('tasks-list') ? 'show' : ''}}{{request()->is('tasks/category/lists') ? 'show' : ''}}{{request()->is('tasks/sub-category/lists') ? 'show' : ''}} " data-bs-parent="#sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('tasks-list') ? '' : 'collapsed'}}" href="{{ route('main_tasks_list') }}">
                        <i class="bi bi-list-task"></i>
                        <span style="margin-top:-3px;">Tasks Lists</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('tasks/category/lists') ? '' : 'collapsed'}}" href="{{ route('task_category_list') }}">
                        <i class="bi bi-box"></i>
                        <span style="margin-top:-3px;">Task Category</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('tasks/sub-category/lists') ? '' : 'collapsed'}}" href="{{ route('task_subcategory_list') }}">
                        <i class="bi bi-boxes"></i>
                        <span style="margin-top:-3px;">Task Sub-Category</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center collapsed" data-bs-target="#userSlideDown" data-bs-toggle="collapse" href="#">
                <i class="bi bi-person"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="userSlideDown" class="nav-content collapse {{request()->is('user/lists') ? 'show' : ''}}{{request()->is('user/members/lists') ? 'show' : ''}}{{request()->is('users/lists') ? 'show' : ''}}" data-bs-parent="#sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('user/lists') ? '' : 'collapsed'}}" href="{{ route('user_list_view') }}">
                        <i class="bi bi-person"></i>
                        <span style="margin-top:-3px;">Registered Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('user/members/lists') ? '' : 'collapsed'}}" href="{{ route('members_lists') }}">
                        <i class="bi bi-person"></i>
                        <span style="margin-top:-3px;">User Members</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('users/lists') ? '' : 'collapsed'}}" href="{{ route('user_list_for_ff') }}">
                        <i class="bi bi-person"></i>
                        <span style="margin-top:-3px;">Users Following & Followers</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center collapsed" data-bs-target="#SSQSlideDown" data-bs-toggle="collapse" href="#">
                <i class="bi bi-question-circle"></i><span>Splash Screen Questions</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="SSQSlideDown" class="nav-content collapse {{request()->is('splash-screen-questions/lists') ? 'show' : ''}}{{request()->is('splash-screen-questions/user-answers-list') ? 'show' : ''}}" data-bs-parent="#sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('splash-screen-questions/lists') ? '' : 'collapsed'}}" href="{{ route('splash_screen_questions') }}">
                        <i class="bi bi-question-circle"></i>
                        <span style="margin-top:-3px;">Questions List</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3 d-flex align-items-center {{request()->is('splash-screen-questions/user-answers-list') ? '' : 'collapsed'}}" href="{{ route('ssq_user_answers_list') }}">
                        <i class="bi bi-question-circle"></i>
                        <span style="margin-top:-3px;">User Answers List</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center {{request()->is('point_calculations') ? '' : 'collapsed'}}" href="{{ route('point_calculation_view') }}">
                <i class="bi bi-calculator"></i>
                <span style="margin-top:-3px;">Point Calculations</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center {{request()->is('emails-sends/user_list') ? '' : 'collapsed'}}" href="{{ route('user_list_for_emails') }}">
                <i class="bi bi-envelope"></i>
                <span style="margin-top:-3px;">Email Sends</span>
            </a>
        </li>
      	<li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center {{request()->is('slogans/list') ? '' : 'collapsed'}}" href="{{ route('slogan_list') }}">
                <i class="bi bi-megaphone"></i>
                <span style="margin-top:-3px;">Slogans</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center {{request()->is('feedback-list') ? '' : 'collapsed'}}" href="{{ route('feedback_list') }}">
                <i class="bi bi-wechat"></i>
                <span style="margin-top:-3px;">User App Feedback</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link p-3 d-flex align-items-center {{request()->is('app-settings') ? '' : 'collapsed'}}" href="{{ route('app_settings') }}">
                <i class="bi bi-phone-flip"></i>
                <span style="margin-top:-3px;">App Settings</span>
            </a>
        </li>
    </ul>
</aside>
