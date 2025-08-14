    {{-- resources/views/partials/sidebar.blade.php --}}
    @auth
        @php
            // نستخدم ?-> للتفادي لو العلاقة لم تُحمَّل لسببٍ ما
            $role = auth()->user()->role?->name;
        @endphp

        <div class="sidebar">
            <ul>
                @switch($role)
                    @case('super admin')
                        <li><a href="{{ url('/super-admin/dashboard') }}">الرئيسية</a></li>
                        <li><a href="{{ url('/super-admin/dashboard') }}">لوحة المالك</a></li>
                        <li><a href="{{ url('/super-admin/users') }}">إدارة المستخدمين</a></li>
                        <li><a href="{{ url('/super-admin/settings') }}">إعدادات النظام</a></li>
                        <li><a href="{{ url('/super-admin/institutes') }}">إعدادات المعاهد</a></li>
                        @break

                    @case('admin')
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/dashboard')) active @endif"
                           href="{{ url('/admin/dashboard') }}">
                            <i class="bi bi-speedometer2"></i> الرئيسية
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/dashboard')) active @endif"
                           href="{{ url('/admin/dashboard') }}">
                            <i class="bi bi-columns-gap"></i> لوحة المشرف
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/subjects*')) active @endif"
                           href="{{ url('/admin/subjects') }}">
                            <i class="bi bi-journal-bookmark"></i> المواد
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/teachers*')) active @endif"
                           href="{{ url('/admin/teachers') }}">
                            <i class="bi bi-people"></i> المدرّسون
                        </a>
                    </li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/students*')) active @endif"
                           href="{{ url('/admin/students') }}">
                            <i class="bi bi-person-lines-fill"></i> الطلاب
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/classes*')) active @endif"
                           href="{{ url('/admin/classes') }}">
                            <i class="bi bi-easel2"></i>الحلقات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/reports')) active @endif"
                           href="{{ url('/admin/reports') }}">
                            <i class="bi bi-bar-chart"></i> التقارير
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/guardians*')) active @endif"
                           href="{{ url('/admin/guardians') }}">
                            <i class="bi bi-people"></i> أولياء الأمور
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin/schedules*')) active @endif"
                           href="{{ route('admin.schedules.index') }}">
                            <i class="bi bi-calendar3"></i> جداول المواعيد
                        </a>
                    </li>
                    @break


                    @case('institute manager')
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/dashboard')) active @endif"
                           href="{{ route('manager.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/admins*')) active @endif"
                           href="{{ url('/manager/admins') }}">
                            <i class="bi bi-person-badge"></i> المشرفون
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/teachers*')) active @endif"
                           href="{{ url('/manager/teachers') }}">
                            <i class="bi bi-people"></i> المدرّسون
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/subjects*')) active @endif"
                           href="{{ url('/manager/subjects') }}">
                            <i class="bi bi-journal-bookmark"></i> المواد
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/classes*')) active @endif"
                           href="{{ route('manager.classes.index') }}">
                            <i class="bi bi-easel2"></i> الحلقات
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/students*')) active @endif"
                           href="{{ url('/manager/students') }}">
                            <i class="bi bi-person-lines-fill"></i> الطلاب
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/guardians*')) active @endif"
                           href="{{ url('/manager/guardians') }}">
                            <i class="bi bi-person-badge"></i> أولياء الأمور
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/schedules*')) active @endif"
                           href="{{ route('manager.schedules.index') }}">
                            <i class="bi bi-calendar3"></i> جداول المواعيد
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/library*')) active @endif"
                           href="{{ route('manager.library.index') }}">
                            <i class="bi bi-book"></i> المكتبة الإلكترونية
                        </a>
                    </li>

                    @break


                    @case('teacher')
                       <li class="{{ request()->is('teacher/dashboard') ? 'active' : '' }}">
                            <a href="{{ url('teacher/dashboard') }}">
                                <i class="bi bi-house-door-fill"></i>
                                الرئيسية
                            </a>
                        </li>

                        <li class="{{ request()->is('teacher/announcements*') ? 'active' : '' }}">
                            <a href="{{ route('teacher.announcements.index') }}">
                                <i class="bi bi-megaphone-fill"></i>
                                إعلاناتي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.schedule.index') }}" class="nav-link">
                                <i class="bi bi-calendar-week"></i>
                                الجدول الأسبوعي
                            </a>
                        </li>

                         <li class="nav-item {{ request()->is('teacher/students') ? 'active' : '' }}">
                            <a href="{{ route('teacher.students.index') }}" class="nav-link">
                                <i class="bi bi-people-fill"></i>
                                <span>جميع الطلاب</span>
                            </a>
                        </li>

                        <li class="{{ request()->is('teacher/classes') ? 'active' : '' }}">
                            <a href="{{ route('teacher.classes.index') }}">
                                <i class="bi bi-journal-bookmark-fill"></i>
                                حلقاتي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('teacher/library*')) active @endif"
                               href="{{ route('teacher.library.index') }}">
                                <i class="bi bi-book"></i> المكتبة الإلكترونية
                            </a>
                        </li>

                        <li><a href="{{ url('/teacher/dashboard') }}">لوحة المعلم</a></li>
                        <li><a href="{{ url('/teacher/assignments') }}">الواجبات</a></li>
                        @break

                    @default
                        {{-- يمكن إضافة روابط افتراضية هنا --}}
                @endswitch
            </ul>
        </div>
    @endauth
