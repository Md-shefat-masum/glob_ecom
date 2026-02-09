<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PermissionRoutes;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class PermissionRoutesController extends Controller
{
    public function viewAllPermissionRoutes(Request $request)
    {
        if ($request->ajax()) {

            $data = PermissionRoutes::orderBy('route_group_name', 'asc')
                ->orderBy('route_module_name', 'asc')
                ->orderBy('name', 'asc')
                ->get();
            return DataTables::of($data)
                ->editColumn('route_group_name', function ($data) {
                    return '<span class="badge badge-primary">' . ($data->route_group_name ?: 'General') . '</span>';
                })
                ->editColumn('route_module_name', function ($data) {
                    return '<span class="badge badge-info">' . ($data->route_module_name ?: 'Core') . '</span>';
                })
                ->editColumn('method', function ($data) {
                    $methodColors = [
                        'GET' => 'success',
                        'POST' => 'primary',
                        'PUT' => 'warning',
                        'PATCH' => 'info',
                        'DELETE' => 'danger'
                    ];
                    $color = $methodColors[$data->method] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . $data->method . '</span>';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i:s a", strtotime($data->created_at));
                })
                ->editColumn('updated_at', function ($data) {
                    if ($data->updated_at) {
                        return date("Y-m-d h:i:s a", strtotime($data->updated_at));
                    }
                    return '-';
                })
                ->rawColumns(['route_group_name', 'route_module_name', 'method'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('backend.role_permission.permisson_routes');
    }

    public function regeneratePermissionRoutes()
    {
        // 1. Get all modules from web.php require statements
        $webFilePath = base_path('routes/web.php');
        $modules = [];
        if (file_exists($webFilePath)) {
            $content = file_get_contents($webFilePath);
            preg_match_all("/require\s+__DIR__\s*\.\s*'\/([^']+)\.php'\s*;/", $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $routeFile) {
                    $fileName = $routeFile . '.php';
                    if (preg_match('/Routes$/', $routeFile)) {
                        $moduleName = strtolower(preg_replace('/Routes$/', '', $routeFile));
                    } else {
                        $moduleName = strtolower($routeFile);
                    }
                    $modules[$moduleName] = base_path('routes/' . $fileName);
                }
            }
        }

        // 2. For each module file, scan for group comments and routes
        $allRoutes = [];
        foreach ($modules as $moduleName => $filePath) {
            if (!file_exists($filePath)) continue;
            $lines = file($filePath);
            $currentGroup = 'General';
            foreach ($lines as $line) {
                $trimmed = trim($line);
                // Detect group comment
                if (preg_match('/^\/\/\s*(.+?)(?:\s+routes?|\s+start|\s+begin)?\s*$/i', $trimmed, $m)) {
                    $groupName = trim($m[1]);
                    if ($groupName && stripos($groupName, 'route') === false) {
                        $currentGroup = ucwords($groupName);
                    }
                }
                // Detect Route::... definition
                if (preg_match('/Route::(get|post|put|patch|delete|options)\s*\(/i', $trimmed)) {
                    // Try to extract route name
                    $name = null;
                    if (preg_match("/->name\(['\"]([^'\"]+)['\"]\)/", $trimmed, $nm)) {
                        $name = $nm[1];
                    }
                    $allRoutes[] = [
                        'module' => $moduleName,
                        'group' => $currentGroup,
                        'line' => $trimmed,
                        'name' => $name
                    ];
                }
            }
        }

        // 3. Remove all old permission routes
        DB::table('permission_routes')->truncate();

        // 4. Insert new permission routes
        $now = now();
        foreach ($allRoutes as $route) {
            // Try to extract method and uri
            if (preg_match('/Route::(get|post|put|patch|delete|options)\s*\(\s*["\']([^"\']+)["\']/', $route['line'], $rm)) {
                $method = strtoupper($rm[1]);
                $uri = $rm[2];
            } else {
                continue;
            }
            DB::table('permission_routes')->insert([
                'route' => $uri,
                'name' => $route['name'] ?? '',
                'method' => $method,
                'route_group_name' => $route['group'],
                'route_module_name' => $route['module'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        Toastr::success("Permission Routes Regenerated Successfully! ", "Success");
        return back();
    }

    /**
     * Get route modules from web.php require statements
     */
    private function getRouteModulesFromWebFile()
    {
        $webFilePath = base_path('routes/web.php');
        $modules = [];
        if (file_exists($webFilePath)) {
            $content = file_get_contents($webFilePath);
            // Match all require statements: require __DIR__.'/filename.php';
            preg_match_all("/require\s+__DIR__\s*\.\s*'\/([^']+)\.php'\s*;/", $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $routeFile) {
                    $fileName = $routeFile . '.php';
                    // Remove 'Routes' suffix only if present at the end, then convert to lowercase
                    if (preg_match('/Routes$/', $routeFile)) {
                        $moduleName = strtolower(preg_replace('/Routes$/', '', $routeFile));
                    } else {
                        $moduleName = strtolower($routeFile);
                    }
                    $modules[$fileName] = $moduleName;
                }
            }
        }
        return $modules;
    }

    /**
     * Extract group name from route file comments - reads comments sequentially
     */
    private function extractGroupFromRouteFile($routeFilePath, $routeName = null)
    {
        if (!file_exists($routeFilePath)) {
            return null;
        }

        $content = file_get_contents($routeFilePath);
        $lines = explode("\n", $content);
        $currentGroup = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Check for group comment patterns
            if (preg_match('/^\/\/\s*(.+?)(?:\s+routes?|\s+start|\s+begin)?(?:\s*$)/i', $line, $matches)) {
                $groupName = trim($matches[1]);

                // Skip lines that are clearly not group names
                if (
                    strpos(strtolower($groupName), 'route') === false &&
                    strlen($groupName) > 2 &&
                    !preg_match('/^\s*(use|include|require|namespace)/i', $groupName)
                ) {
                    $currentGroup = ucwords($groupName);
                }
            }

            // If we find the specific route name, return the current group
            if ($routeName && strpos($line, "'" . $routeName . "'") !== false) {
                return $currentGroup;
            }
        }

        // If no specific route name provided, return the first group found
        return $currentGroup;
    }

    /**
     * Determine the route group based on controller and file comments
     */
    private function determineRouteGroup($route)
    {
        $controller = $route->getController();
        if (!$controller) {
            return 'General';
        }

        $controllerClass = get_class($controller);
        $controllerName = class_basename($controllerClass);
        $routeName = $route->getName();

        // Get all module route files dynamically
        $modules = $this->getRouteModulesFromWebFile();

        foreach ($modules as $fileName => $module) {
            $filePath = base_path('routes/' . $fileName);
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                // Check if this controller is used in this route file
                if (strpos($content, $controllerName) !== false) {
                    $group = $this->extractGroupFromRouteFile($filePath, $routeName);
                    if ($group) {
                        return $group;
                    }
                }
            }
        }

        // Fallback: extract from route name
        if ($routeName) {
            $parts = explode('.', $routeName);
            if (count($parts) > 1) {
                return ucfirst($parts[0]);
            }
        }

        return 'General';
    }

    /**
     * Determine the route module based on the file the route is defined in
     */
    private function determineRouteModule($route)
    {
        $modules = $this->getRouteModulesFromWebFile();
        $routeFilePath = '';
        if (method_exists($route, 'getAction')) {
            $action = $route->getAction();
            if (isset($action['file'])) {
                $routeFilePath = $action['file'];
            }
        }
        // Strictly match the file path to the module
        foreach ($modules as $fileName => $module) {
            if (!empty($routeFilePath) && str_ends_with($routeFilePath, DIRECTORY_SEPARATOR . $fileName)) {
                return $module;
            }
        }
        // If not found, it's not from a module file, so treat as 'general'
        return 'general';
    }

    /**
     * Get routes grouped by route group name and module name
     */
    public function getRoutesByGroup()
    {
        $routeGroups = PermissionRoutes::selectRaw('route_group_name, route_module_name, COUNT(*) as count')
            ->groupBy('route_group_name', 'route_module_name')
            ->orderBy('route_group_name')
            ->orderBy('route_module_name')
            ->get();

        $groupedRoutes = [];
        foreach ($routeGroups as $group) {
            $routes = PermissionRoutes::where('route_group_name', $group->route_group_name)
                ->where('route_module_name', $group->route_module_name)
                ->orderBy('name')
                ->get();

            $groupKey = $group->route_group_name;
            $moduleKey = $group->route_module_name ?: 'Core';

            if (!isset($groupedRoutes[$groupKey])) {
                $groupedRoutes[$groupKey] = [
                    'total_count' => 0,
                    'modules' => []
                ];
            }

            $groupedRoutes[$groupKey]['modules'][$moduleKey] = [
                'count' => $group->count,
                'routes' => $routes
            ];
            $groupedRoutes[$groupKey]['total_count'] += $group->count;
        }

        return response()->json($groupedRoutes);
    }

    /**
     * Get routes grouped by module name only
     */
    public function getRoutesByModule()
    {
        $routeModules = PermissionRoutes::selectRaw('route_module_name, COUNT(*) as count')
            ->groupBy('route_module_name')
            ->orderBy('route_module_name')
            ->get();

        $moduleRoutes = [];
        foreach ($routeModules as $module) {
            $routes = PermissionRoutes::where('route_module_name', $module->route_module_name)
                ->orderBy('route_group_name')
                ->orderBy('name')
                ->get();
            $moduleRoutes[$module->route_module_name ?: 'Core'] = [
                'count' => $module->count,
                'routes' => $routes
            ];
        }

        return response()->json($moduleRoutes);
    }

    /**
     * Get routes organized by Module > Groups > Routes structure
     */
    public function getRoutesByModuleAndGroup()
    {
        $routes = PermissionRoutes::orderBy('route_module_name')
            ->orderBy('route_group_name')
            ->orderBy('name')
            ->get();

        $moduleGroupRoutes = [];

        foreach ($routes as $route) {
            $moduleName = $route->route_module_name ?: 'general';
            $groupName = $route->route_group_name ?: 'General';

            // Initialize module if not exists
            if (!isset($moduleGroupRoutes[$moduleName])) {
                $moduleGroupRoutes[$moduleName] = [
                    'total_count' => 0,
                    'groups' => []
                ];
            }

            // Initialize group if not exists
            if (!isset($moduleGroupRoutes[$moduleName]['groups'][$groupName])) {
                $moduleGroupRoutes[$moduleName]['groups'][$groupName] = [
                    'count' => 0,
                    'routes' => []
                ];
            }

            // Add route to group
            $moduleGroupRoutes[$moduleName]['groups'][$groupName]['routes'][] = $route;
            $moduleGroupRoutes[$moduleName]['groups'][$groupName]['count']++;
            $moduleGroupRoutes[$moduleName]['total_count']++;
        }

        return $moduleGroupRoutes;
    }
}
