<!-- resources/views/libraries/index.blade.php -->
@extends('layouts.app')

@section('title','إدارة المكتبة')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h1 class="display-6 fw-bold mb-3 mb-md-0">إدارة المكتبة الإلكترونية</h1>

            @if(Auth::user()->hasAnyRole(['super admin','institute manager','admin','teacher']))
                <a href="{{ route(
                    Auth::user()->hasRole('super admin') ? 'super-admin.library.create' :
                    (Auth::user()->hasRole('institute manager') ? 'manager.library.create' :
                    (Auth::user()->hasRole('admin') ? 'admin.library.create' : 'teacher.library.create'))
                ) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة مورد جديد
                </a>
            @endif
        </div>

        {{-- Success message display --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Search and Filter Form --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form id="libraryFilterForm" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" placeholder="بحث بالاسم أو الوصف أو التصنيف..."
                               value="{{ request('search') }}"
                               class="form-control" id="searchInput">
                    </div>
                    <div class="col-md-4">
                        <select name="category_id" class="form-select" id="categorySelect">
                            <option value="">جميع التصنيفات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i> بحث
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Library Items Table --}}
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="text-end">الاسم</th>
                        <th class="text-end">التصنيف</th>
                        <th class="text-end">المؤلف</th>
                        <th class="text-end">الناشر</th>
                        <th class="text-end">الحالة</th>
                        <th class="text-end">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody id="libraryItemsContainer">
                    {{-- Initial content will be loaded by AJAX on document ready --}}
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin me-2"></i> جارٍ تحميل البيانات...
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer" id="paginationContainer">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" xintegrity="sha512-fzff82+8pzHnwA1mQ0dzz9/E0B+ZRizq08yZfya66INZBz86qKTCt9MLU0NCNIgaMJCgeyhujhasnFUsYMsi0Q==" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            // Debounce function to limit API calls
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        func.apply(context, args);
                    }, wait);
                };
            }

            // Function to fetch filtered results
            function fetchFilteredResults(page = 1) {
                const searchInput = $('#searchInput').val();
                const categorySelect = $('#categorySelect').val();
                const url = getApiBaseUrl();

                $('#libraryItemsContainer').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin me-2"></i> جارٍ تحميل البيانات...
                        </td>
                    </tr>
                `);
                $('#paginationContainer').html(''); // Clear pagination during load

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        search: searchInput,
                        category_id: categorySelect,
                        page: page
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    success: function(response) {
                        $('#libraryItemsContainer').html(response.html);
                        $('#paginationContainer').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.error('Error fetching library data:', xhr.responseText);
                        $('#libraryItemsContainer').html(`
                            <tr>
                                <td colspan="6" class="text-center py-4 text-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i> حدث خطأ أثناء تحميل البيانات.
                                </td>
                            </tr>
                        `);
                        $('#paginationContainer').html('');
                    }
                });
            }

            // Get the base API URL based on the current user's role
            function getApiBaseUrl() {
                @if(Auth::user()->hasRole('super admin'))
                    return "{{ route('super-admin.institutes.library.api.index') }}";
                @elseif(Auth::user()->hasRole('institute manager'))
                    return "{{ route('manager.library.api.index') }}";
                @elseif(Auth::user()->hasRole('admin'))
                    return "{{ route('admin.library.api.index') }}";
                @elseif(Auth::user()->hasRole('teacher'))
                    return "{{ route('teacher.library.api.index') }}";
                @else
                    return "";
                @endif
            }

            $('#searchInput').on('input', debounce(function() {
                fetchFilteredResults();
            }, 300));

            $('#categorySelect').on('change', function() {
                fetchFilteredResults();
            });

            // Handle pagination clicks using event delegation
            $(document).on('click', '#paginationContainer .pagination a', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                fetchFilteredResults(page);
            });

            // Prevent default form submission on the search form
            $('#libraryFilterForm').on('submit', function(e) {
                e.preventDefault();
                fetchFilteredResults();
            });

            // Initial fetch on page load
            fetchFilteredResults();
        });
    </script>
@endpush
