{{-- resources/views/partials/sidebar.blade.php --}}
@auth
@php
    // نحصل على اسم الدور بأمان
    $role = auth()->user()->role?->name;
@endphp

<div class="sidebar">
    <ul>
        @switch($role)

            {{-- ===================== SUPER ADMIN ===================== --}}
            @case('super admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('super-admin/dashboard') ? 'active' : '' }}"
                       href="{{ url('/super-admin/dashboard') }}">
                        <span><i class="bi bi-speedometer2 me-2"></i> الرئيسية</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('super-admin/managers*') ? 'active' : '' }}"
                       href="{{ url('/super-admin/managers') }}">
                        <span><i class="bi bi-people-fill me-2"></i> إدارة مدراء المعاهد</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('super-admin/institutes*') ? 'active' : '' }}"
                       href="{{ url('/super-admin/institutes') }}">
                        <span><i class="bi bi-gear me-2"></i> إعدادات المعاهد</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('super-admin/certificates*') ? 'active' : '' }}"
                       href="{{ route('super-admin.certificates.index') }}">
                        <span><i class="bi bi-award me-2"></i> طلبات الشهادات</span>
                    </a>
                </li>
            @break

            {{-- ===================== ADMIN ===================== --}}
            @case('admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/profile') ? 'active' : '' }}"
                       href="{{ route('admin.profile.show', request()->has('institute_id') ? ['institute_id'=>request('institute_id')] : []) }}">
                        <span><i class="bi bi-person-circle"></i> بروفايلي</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin/announcements*') ? 'active' : '' }}">
                    <a href="{{ route('admin.announcements.index') }}" class="nav-link">
                        <span><i class="bi bi-megaphone-fill"></i> إعلاناتي</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.announcements.inbox', request()->has('institute_id') ? ['institute_id'=>request('institute_id')] : []) }}"
                       class="nav-link">
                        <span><i class="bi bi-inbox"></i> إعلانات موجهة لي</span>
                        <span id="admin-inbox-count" class="badge bg-danger rounded-pill d-none ms-2 notif-badge">0</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('admin/subjects*')) active @endif"
                       href="{{ url('/admin/subjects') }}">
                        <span><i class="bi bi-journal-bookmark"></i> المواد</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('admin/teachers*')) active @endif"
                       href="{{ url('/admin/teachers') }}">
                        <span><i class="bi bi-people"></i> المدرّسون</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('admin/students*')) active @endif"
                       href="{{ url('/admin/students') }}">
                        <span><i class="bi bi-person-lines-fill"></i> الطلاب</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('admin/classes*')) active @endif"
                       href="{{ url('/admin/classes') }}">
                        <span><i class="bi bi-easel2"></i> الحلقات</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('admin/guardians*')) active @endif"
                       href="{{ url('/admin/guardians') }}">
                        <span><i class="bi bi-people"></i> أولياء الأمور</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('admin/schedules*')) active @endif"
                       href="{{ route('admin.schedules.index') }}">
                        <span><i class="bi bi-calendar3"></i> جداول المواعيد</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/library*')) active @endif"
                       href="{{ route('admin.library.index') }}">
                        <span><i class="bi bi-book"></i> المكتبة الإلكترونية</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('attendance.scan') ? 'active' : '' }}"
                       href="{{ route('attendance.scan') }}">
                        <span><i class="bi bi-qr-code-scan"></i> تسجيل حضور</span>
                    </a>
                </li>
            @break

            {{-- ===================== INSTITUTE MANAGER ===================== --}}
            @case('institute manager')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('manager/profile') ? 'active' : '' }}"
                       href="{{ route('manager.profile.show') }}">
                        <span><i class="bi bi-person-circle"></i> بروفايلي</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('manager/announcements*') ? 'active' : '' }}">
                    <a href="{{ route('manager.announcements.index') }}" class="nav-link">
                        <span><i class="bi bi-megaphone-fill"></i> إعلاناتي</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('manager.announcements.inbox') }}" class="nav-link">
                        <span><i class="bi bi-inbox"></i> إعلانات موجهة لي</span>
                        <span id="manager-inbox-count" class="badge bg-danger rounded-pill d-none ms-2 notif-badge">0</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/admins*')) active @endif"
                       href="{{ url('/manager/admins') }}">
                        <span><i class="bi bi-person-badge"></i> المشرفون</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/teachers*')) active @endif"
                       href="{{ url('/manager/teachers') }}">
                        <span><i class="bi bi-people"></i> المدرّسون</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/subjects*')) active @endif"
                       href="{{ url('/manager/subjects') }}">
                        <span><i class="bi bi-journal-bookmark"></i> المواد</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/classes*')) active @endif"
                       href="{{ route('manager.classes.index') }}">
                        <span><i class="bi bi-easel2"></i> الحلقات</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/students*')) active @endif"
                       href="{{ url('/manager/students') }}">
                        <span><i class="bi bi-person-lines-fill"></i> الطلاب</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/guardians*')) active @endif"
                       href="{{ url('/manager/guardians') }}">
                        <span><i class="bi bi-person-badge"></i> أولياء الأمور</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/schedules*')) active @endif"
                       href="{{ route('manager.schedules.index') }}">
                        <span><i class="bi bi-calendar3"></i> جداول المواعيد</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/library*')) active @endif"
                       href="{{ route('manager.library.index') }}">
                        <span><i class="bi bi-book"></i> المكتبة الإلكترونية</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('manager/certificates*')) active @endif"
                       href="{{ route('manager.certificates.index') }}">
                        <span><i class="bi bi-award"></i> الشهادات</span>
                    </a>
                </li>
            @break

            {{-- ===================== TEACHER ===================== --}}
            @case('teacher')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/profile') ? 'active' : '' }}"
                       href="{{ route('teacher.profile.show', request()->has('institute_id') ? ['institute_id'=>request('institute_id')] : []) }}">
                        <span><i class="bi bi-person-circle"></i> بروفايلي</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('teacher/announcements*') ? 'active' : '' }}">
                    <a href="{{ route('teacher.announcements.index') }}" class="nav-link">
                        <span><i class="bi bi-megaphone-fill"></i> إعلاناتي</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('teacher.announcements.inbox') }}" class="nav-link">
                        <span><i class="bi bi-inbox"></i> إعلانات موجهة لي</span>
                        <span id="teacher-inbox-count" class="badge bg-danger rounded-pill d-none ms-2 notif-badge">0</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('teacher.schedule.index') }}" class="nav-link">
                        <span><i class="bi bi-calendar-week"></i> الجدول الأسبوعي</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('teacher/students') ? 'active' : '' }}">
                    <a href="{{ route('teacher.students.index') }}" class="nav-link">
                        <span><i class="bi bi-people-fill"></i> جميع الطلاب</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('teacher/classes') ? 'active' : '' }}">
                    <a href="{{ route('teacher.classes.index') }}" class="nav-link">
                        <span><i class="bi bi-journal-bookmark-fill"></i> حلقاتي</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->is('teacher/library*')) active @endif"
                       href="{{ route('teacher.library.index') }}">
                        <span><i class="bi bi-book"></i> المكتبة الإلكترونية</span>
                    </a>
                </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('attendance.scan') ? 'active' : '' }}"
                   href="{{ route('attendance.scan.teacher') }}">
                    <span><i class="bi bi-qr-code-scan"></i> تسجيل حضور</span>
                </a>
            </li>
            @break

            @default
                {{-- يمكن إضافة روابط افتراضية هنا --}}
        @endswitch
    </ul>
</div>

{{-- شِيبس بسيطة لشارة الإشعارات --}}
<style>
    .sidebar .nav-link {
        display: flex;
        align-items: center;
        justify-content: space-between; /* النص يسار والبادج يمين (تلقائيًا مع RTL) */
        gap: .5rem;
    }
    .notif-badge {
        font-size: .75rem;
        line-height: 1;
        padding: .35rem .5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const role = @json($role);

  async function setBadge(elId, url) {
    const el = document.getElementById(elId);
    if (!el || !url) return;
    try {
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        credentials: 'same-origin'
      });
      if (!res.ok) { console.warn('unread-count status', res.status); return; }
      const data = await res.json();
      const count = Number(data?.count ?? 0);
      if (count > 0) {
        el.textContent = count > 99 ? '99+' : count;
        el.classList.remove('d-none');
      } else {
        el.classList.add('d-none');
      }
    } catch (e) {
      console.warn('unread-count fetch error:', e);
    }
  }

  if (role === 'admin') {
    setBadge('admin-inbox-count', @json(route('admin.announcements.inbox.unreadCount', request()->has('institute_id') ? ['institute_id'=>request('institute_id')] : [])));
  } else if (role === 'institute manager') {
    setBadge('manager-inbox-count', @json(route('manager.announcements.inbox.unreadCount')));
  } else if (role === 'teacher') {
    setBadge('teacher-inbox-count', @json(route('teacher.announcements.inbox.unreadCount')));
  }
});
</script>

@endauth
