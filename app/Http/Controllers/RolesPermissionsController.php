<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use Inertia\Inertia;

class RolesPermissionsController extends Controller
{
    public function index()
    {
        $roles = [
            'Head Frontliner' => array_map(fn ($p) => $p->value, UserPermission::getHeadFrontlinerPermissions()),
            'Frontliner'      => array_map(fn ($p) => $p->value, UserPermission::getFrontlinerPermissions()),
            'Dispatcher'      => array_map(fn ($p) => $p->value, UserPermission::getDispatcherPermissions()),
            'Team Lead'       => array_map(fn ($p) => $p->value, UserPermission::getTeamLeaderPermissions()),
            'Consultant'      => array_map(fn ($p) => $p->value, UserPermission::getConsultantPermissions()),
            'Human Resource'  => array_map(fn ($p) => $p->value, UserPermission::getHumanResourcePermissions()),
            'IT Admin'        => array_map(fn ($p) => $p->value, UserPermission::getITAdminPermissions()),
            'Employee'        => array_map(fn ($p) => $p->value, UserPermission::getRegularPermissions()),
        ];

        $permissionGroups = [
            [
                'group'       => 'Job Order',
                'icon'        => 'ClipboardListIcon',
                'permissions' => [
                    UserPermission::CreateJobOrder->value,
                    UserPermission::UpdateJobOrder->value,
                    UserPermission::CreateJobOrderCorrection->value,
                    UserPermission::UpdateJobOrderCorrection->value,
                    UserPermission::ViewAnyJobOrder->value,
                    UserPermission::ViewAnyJobOrderCorrection->value,
                    UserPermission::RestoreArchivedJobOrder->value,
                    UserPermission::ForceDeleteJobOrder->value,
                ],
            ],
            [
                'group'       => 'Hauling Operations',
                'icon'        => 'GaugeCircleIcon',
                'permissions' => [
                    UserPermission::FillOutSafetyInspectionChecklist->value,
                    UserPermission::AssignHaulingPersonnel->value,
                    UserPermission::AssignAppraisers->value,
                    UserPermission::SetHaulingDuration->value,
                ],
            ],
            [
                'group'       => 'Truck',
                'icon'        => 'Truck',
                'permissions' => [
                    UserPermission::AddNewTruck,
                    UserPermission::UpdateTruck,
                    UserPermission::ArchiveTruck,
                    UserPermission::RestoreTruck,
                    UserPermission::ForceDeleteTruck,
                ],
            ],
            [
                'group'       => 'Incident Report',
                'icon'        => 'FilePenLineIcon',
                'permissions' => [
                    UserPermission::ManageIncidentReports->value,
                ],
            ],
            [
                'group'       => 'Performance & Analytics',
                'icon'        => 'AwardIcon',
                'permissions' => [
                    UserPermission::ViewReportsAnalytics->value,
                    UserPermission::ViewPerformances->value,
                    UserPermission::ViewAnyEmployeeRating->value,
                    UserPermission::CreateEmployeeRating->value,
                    UserPermission::UpdateEmployeeRating->value,
                    UserPermission::DeleteEmployeeRating->value,
                    UserPermission::ExportEmployeeRating->value,
                ],
            ],
            [
                'group'       => 'Employee Management',
                'icon'        => 'UsersRoundIcon',
                'permissions' => [
                    UserPermission::ManageEmployees->value,
                ],
            ],
            [
                'group'       => 'User Management',
                'icon'        => 'UserRoundCog',
                'permissions' => [
                    UserPermission::ManageUsers->value,
                ],
            ],
            [
                'group'       => 'System',
                'icon'        => 'LayoutDashboardIcon',
                'permissions' => [
                    UserPermission::ViewActivityLogs->value,
                ],
            ],
        ];

        $permissionLabels = [];
        foreach (UserPermission::cases() as $permission) {
            $permissionLabels[$permission->value] = $permission->getLabel();
        }

        return Inertia::render('roles-and-permissions/Index', [
            'roles'            => $roles,
            'permissionGroups' => $permissionGroups,
            'permissionLabels' => $permissionLabels,
        ]);
    }
}
