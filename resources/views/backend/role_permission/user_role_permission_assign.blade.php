@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    User Role Permission
@endsection
@section('page_heading')
    Assign User Role Permission
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    {{-- <h4 class="card-title mb-3">Assign Role to this User</h4> --}}

                    <form class="needs-validation" method="POST" action="{{ url('save/assigned/role/permission') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $userId }}">

                        @php
                            $userRoles = App\Models\UserRole::orderBy('id', 'desc')->get();
                        @endphp

                        {{-- <div class="row">
                            <div class="col-lg-12">
                                @foreach ($userRoles as $userRoles)
                                    @php
                                        $permissionsUnderRole = '';
                                        $rolePermissions = App\Models\RolePermission::where(
                                            'role_id',
                                            $userRoles->id,
                                        )->get();
                                        foreach ($rolePermissions as $rolePermission) {
                                            $permissionsUnderRole .= $rolePermission->route_name . ', ';
                                        }
                                    @endphp
                                    <div class="form-group border-bottom">
                                        <table>
                                            <tr>
                                                <td style="padding-right: 10px; vertical-align: middle;">
                                                    <input type="checkbox" @if (App\Models\UserRolePermission::where('user_id', $userId)->where('role_id', $userRoles->id)->exists()) checked @endif
                                                        data-size="small" id="role{{ $userRoles->id }}"
                                                        value="{{ $userRoles->id }}" name="role_id[]"
                                                        data-toggle="switchery" data-color="#08da82"
                                                        data-secondary-color="#df3554" />
                                                </td>
                                                <td style="padding-top: 5px; vertical-align: middle;">
                                                    <label for="role{{ $userRoles->id }}" style="cursor: pointer">
                                                        {{ $userRoles->name }} [ {{ rtrim($permissionsUnderRole, ', ') }} ]
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div> --}}


                        @php
                            use Illuminate\Support\Str;
                            // Get all permissions organized by module and group
                            $permissionController = new App\Http\Controllers\PermissionRoutesController();
                            $moduleGroupRoutes = $permissionController->getRoutesByModuleAndGroup();
                            $userPermissions = App\Models\UserRolePermission::where('user_id', $userId)
                                ->pluck('permission_id')
                                ->toArray();
                            $homeRoute = App\Models\PermissionRoutes::where('route', 'home')->first();
                        @endphp

                        <h4 class="card-title mb-4 mt-4">Assign Permission to this User</h4>

                        <!-- Auto-check home route if exists -->
                        @if ($homeRoute && in_array($homeRoute->id, $userPermissions))
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
                                                    <div class="d-flex align-items-center mb-2">
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
                                                                            @if (in_array($permissionRoute->id, $userPermissions)) checked @endif
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
                            <button class="btn btn-primary m-2" type="submit"><i class="fas fa-save"></i>&nbsp; Assign Role
                                Permission</button>
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
        // Initialize all switchery toggles
        $('[data-toggle="switchery"]').each(function(idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });
        // Keep all accordions open by default
        $(document).ready(function() {
            $('.collapse').addClass('show');
        });
        // Group Switchery logic (fixed for robust UI sync)
        setTimeout(function() {
            $('.group-switchery').each(function() {
                var groupClass = $(this).data('group');
                var $groupSwitch = $(this)[0].switchery;
                var $groupCheckbox = $(this);
                var $groupItems = $('.' + groupClass + ' .group-item-checkbox');

                // Set initial state: if all checked, group switch is on
                var allChecked = $groupItems.length > 0 && $groupItems.filter(':checked').length ===
                    $groupItems.length;
                if (allChecked && $groupSwitch) {
                    $groupSwitch.setPosition(true);
                }

                // On group switch change
                $groupCheckbox.on('change', function() {
                    var checked = $groupCheckbox.is(':checked');
                    $groupItems.each(function() {
                        var currentCheckbox = this;
                        var currentSwitchery = currentCheckbox.switchery;
                        if ($(currentCheckbox).is(':checked') !== checked) {
                            // Use native click to sync Switchery UI
                            if (currentCheckbox && currentCheckbox.click) {
                                currentCheckbox.click();
                            } else {
                                $(currentCheckbox).prop('checked', checked);
                            }
                        }
                    });
                });

                // If any item in group is unchecked, turn off group switch
                $groupItems.on('change', function() {
                    var allChecked = $groupItems.length > 0 && $groupItems.filter(':checked')
                        .length === $groupItems.length;
                    if ($groupSwitch) {
                        $groupSwitch.setPosition(allChecked);
                    }
                });
            });
        }, 100); // Small delay to ensure all Switchery instances are initialized
    </script>
@endsection
