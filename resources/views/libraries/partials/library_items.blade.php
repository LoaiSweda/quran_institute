{{-- resources/views/libraries/partials/library_items.blade.php --}}

@forelse ($libraries as $item)
    <tr>
        <td class="text-end">
            <div class="fw-semibold">{{ $item->name }}</div>
            @if($item->description)
                <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
            @endif
        </td>
        <td class="text-end">{{ $item->category->name ?? 'غير مصنف' }}</td>
        <td class="text-end">{{ $item->author ?? '-' }}</td>
        <td class="text-end">
            <div>{{ $item->uploader->name ?? 'N/A' }}</div>
            @if($item->institute)
                <small class="text-muted">{{ $item->institute->name }}</small>
            @endif
        </td>
        <td class="text-end">
            <span class="badge bg-{{ $item->is_visible ? 'success' : 'danger' }}-subtle text-{{ $item->is_visible ? 'success' : 'danger' }}">
                {{ $item->is_visible ? 'مرئي' : 'مخفي' }}
            </span>
        </td>
        <td class="text-end">
            <div class="d-flex justify-content-end gap-2">
                {{-- Download button --}}
                @if($item->file)
                    <a href="{{ $item->file->url }}" target="_blank" class="btn btn-sm btn-outline-primary" title="تحميل">
                        <i class="fas fa-download"></i>
                    </a>
                @endif

                {{-- Edit button (conditional based on role and ownership) --}}
                @if(Auth::user()->hasRole('super admin') || (Auth::user()->hasRole('institute manager') && ($item->institute_id == Auth::user()->institute_id || $item->institute_id == null)) || (Auth::user()->hasRole('teacher') && $item->user_id == Auth::id()))
                    <a href="{{ route(Auth::user()->hasRole('super admin') ? 'super-admin.library.edit' : (Auth::user()->hasRole('institute manager') ? 'manager.library.edit' : 'teacher.library.edit'), $item->id) }}"
                       class="btn btn-sm btn-outline-warning" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </a>
                @endif

                {{-- Toggle Visibility button (Super Admin & Institute Manager only) --}}
                @if(Auth::user()->hasAnyRole(['super admin', 'institute manager']))
                    @if(Auth::user()->hasRole('super admin') || (Auth::user()->hasRole('institute manager') && ($item->institute_id == Auth::user()->institute_id || $item->institute_id == null)))
                        <form action="{{ route(Auth::user()->hasRole('super admin') ? 'super-admin.library.toggle-visibility' : 'manager.library.toggle-visibility', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-{{ $item->is_visible ? 'danger' : 'success' }}" title="{{ $item->is_visible ? 'إخفاء' : 'إظهار' }}">
                                <i class="fas {{ $item->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Delete button (conditional based on role and ownership) --}}
                @if(Auth::user()->hasRole('super admin') || (Auth::user()->hasRole('institute manager') && ($item->institute_id == Auth::user()->institute_id || $item->institute_id == null)) || (Auth::user()->hasRole('teacher') && $item->user_id == Auth::id()))
                    <form action="{{ route(Auth::user()->hasRole('super admin') ? 'super-admin.library.destroy' : (Auth::user()->hasRole('institute manager') ? 'manager.library.destroy' : 'teacher.library.destroy'), $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="fas fa-book-open me-2"></i> لا توجد موارد في المكتبة حالياً
        </td>
    </tr>
@endforelse
