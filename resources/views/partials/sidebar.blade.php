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
                        <li><a href="{{ url('/admin/dashboard') }}">الرئيسية</a></li>
                        <li><a href="{{ url('/admin/dashboard') }}">لوحة المشرف</a></li>
                        <li><a href="{{ url('/admin/reports') }}">التقارير</a></li>
                        @break

                    @case('institute manager')
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/dashboard')) active @endif"
                           href="{{ route('manager.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('manager/classes*')) active @endif"
                           href="{{ url('/manager/classes') }}">
                            <i class="bi bi-collection"></i> الحلقات
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

                        <li><a href="{{ url('/teacher/dashboard') }}">لوحة المعلم</a></li>
                        <li><a href="{{ url('/teacher/assignments') }}">الواجبات</a></li>
                        @break

                    @default
                        {{-- يمكن إضافة روابط افتراضية هنا --}}
                @endswitch
            </ul>
        </div>
    @endauth
