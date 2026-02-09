@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    User Role
@endsection
@section('page_heading')
    Update User Role
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Update User Role Form</h4>

                    <form class="needs-validation" method="POST" action="{{ url('update/user/role') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $userRoleInfo->id }}">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Role Name <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" value="{{ $userRoleInfo->name }}"
                                    id="name" placeholder="Role Name" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Role Description</label>
                            <div class="col-sm-10">
                                <textarea id="description" name="description" class="form-control" placeholder="Role Description Here">{{ $userRoleInfo->description }}</textarea>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        @php
                            use Illuminate\Support\Str;
                            // Get selected permissions for this role
                            $selectedPermissions = App\Models\RolePermission::where('role_id', $userRoleInfo->id)
                                ->pluck('permission_id')
                                ->toArray();
                            // Get routes organized by Module > Groups > Routes structure
                            $permissionController = new App\Http\Controllers\PermissionRoutesController();
                            $moduleGroupRoutes = $permissionController->getRoutesByModuleAndGroup();
                            $homeRoute = App\Models\PermissionRoutes::where('route', 'home')->first();
                        @endphp

                        <h4 class="card-title mb-4 mt-4">Assign Permission to this Role</h4>

                        <!-- Auto-check home route if exists -->
                        @if (
                            $homeRoute &&
                                App\Models\RolePermission::where('role_id', $userRoleInfo->id)->where('permission_id', $homeRoute->id)->exists())
                            <input type="checkbox" checked hidden id="per{{ $homeRoute->id }}" value="{{ $homeRoute->id }}"
                                name="permission_id[]" />
                        @endif

                        <div class="accordion" id="moduleAccordion">
                            @foreach ($moduleGroupRoutes as $moduleName => $moduleData)
                                <div class="card">
                                    <div class="card-header" id="heading{{ Str::slug($moduleName) }}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                                data-target="#collapse{{ Str::slug($moduleName) }}" aria-expanded="true"
                                                aria-controls="collapse{{ Str::slug($moduleName) }}">
                                                <i class="fas fa-cube text-primary"></i>
                                                <strong>{{ ucwords(str_replace(['-', '_'], ' ', $moduleName)) }}
                                                    Module</strong>
                                                <span class="badge badge-primary ml-2">{{ $moduleData['total_count'] }}
                                                    routes</span>
                                                <i class="fas fa-chevron-up float-right mt-1"></i>
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse{{ Str::slug($moduleName) }}" class="collapse show"
                                        aria-labelledby="heading{{ Str::slug($moduleName) }}">
                                        <div class="card-body">
                                            @foreach ($moduleData['groups'] as $groupName => $groupData)
                                                <div class="mb-4">
                                                    <div class="d-flex align-items-center mb-2 ">
                                                        <h6 class="text-info mb-0 mr-2">
                                                            <i class="fas fa-layer-group"></i>
                                                            {{ $groupName }} Group
                                                            <span class="badge badge-info ml-2">{{ $groupData['count'] }}
                                                                routes</span>
                                                        </h6>
                                                        <input type="checkbox" class="ml-3 group-switchery"
                                                            data-group="group-{{ Str::slug($moduleName . '-' . $groupName) }}"
                                                            data-size="small" data-toggle="switchery" data-color="#08da82"
                                                            data-secondary-color="#df3554" style="margin-left: 15px;" />
                                                        <span class="ml-2 text-muted" style="font-size:13px;">All</span>
                                                    </div>
                                                    <div class="row group-{{ Str::slug($moduleName . '-' . $groupName) }}">
                                                        @foreach ($groupData['routes'] as $index => $permissionRoute)
                                                            @if ($permissionRoute->route == 'home')
                                                                @continue
                                                            @endif
                                                            <div class="col-md-6 mb-2">
                                                                <div class="form-group border-bottom pb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <input type="checkbox"
                                                                            @if (in_array($permissionRoute->id, $selectedPermissions)) checked @endif
                                                                            data-size="small"
                                                                            id="per{{ $permissionRoute->id }}"
                                                                            value="{{ $permissionRoute->id }}"
                                                                            name="permission_id[]"
                                                                            class="group-item-checkbox"
                                                                            data-toggle="switchery" data-color="#08da82"
                                                                            data-secondary-color="#df3554" />
                                                                        <div class="ml-3">
                                                                            <label for="per{{ $permissionRoute->id }}"
                                                                                style="cursor: pointer; margin-bottom: 0;">
                                                                                <small class="text-muted">Route:</small>
                                                                                <strong>{{ $permissionRoute->route }}</strong><br>
                                                                                <small class="text-muted">Name:</small>
                                                                                <code>{{ $permissionRoute->name }}</code>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <div class="form-group text-center py-3 mb-0"
                            style="position: fixed; bottom: 0; left: 0; right: 50px; background-color: #fff; z-index: 1000; display: flex; justify-content: end; align-items: center; box-shadow: 0 -2px 4px rgba(0,0,0,0.1);">
                             <button class="btn btn-primary m-2" type="submit"><i class="fas fa-save"></i>&nbsp; Update User
                                Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    @parent
    <script src="{{ url('assets') }}/plugins/switchery/switchery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize all switchery toggles
            $('[data-toggle="switchery"]').each(function(idx, obj) {
                new Switchery($(this)[0], $(this).data());
            });
            $('.collapse').addClass('show');

            // Group Switchery logic using native click for full UI sync
            setTimeout(function() {
                $('.group-switchery').each(function() {
                    var $groupCheckbox = $(this);
                    var groupClass = $groupCheckbox.data('group');
                    var $groupItems = $('.' + groupClass + ' .group-item-checkbox');

                    // Set initial state: if all checked, group switch is on
                    var allChecked = $groupItems.length > 0 && $groupItems.filter(':checked').length === $groupItems.length;
                    if (allChecked && this.switchery) {
                        this.switchery.setPosition(true);
                        $groupCheckbox.prop('checked', true);
                    }

                    // On group switch change
                    $groupCheckbox.on('change', function() {
                        var isChecked = $groupCheckbox.is(':checked');
                        $groupItems.each(function() {
                            // Use native click to sync Switchery UI and state
                            if ($(this).is(':checked') !== isChecked) {
                                this.click(); // always use native click for children
                            }
                        });
                    });

                    // When individual items change, update group switch
                    $groupItems.on('change', function() {
                        var checkedCount = $groupItems.filter(':checked').length;
                        var totalCount = $groupItems.length;
                        var allChecked = checkedCount === totalCount;
                        $groupCheckbox.prop('checked', allChecked);
                        // Only update group switchery UI if instance exists
                        if ($groupCheckbox[0] && $groupCheckbox[0].switchery) {
                            $groupCheckbox[0].switchery.setPosition(allChecked);
                        }
                    });
                });
            }, 500); // Delay to ensure all Switchery instances are initialized
        });
    </script>
@endsection
