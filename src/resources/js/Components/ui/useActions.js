import {router} from "@inertiajs/vue3";
import {PlusIcon, PencilIcon, DownloadIcon, HistoryIcon, EyeIcon,} from "lucide-vue-next";

export const patientActions = (patient) => [
    {
        label: 'New Patient',
        icon: PlusIcon,
        permission: { module: 'patients', action: 'create' },
        onClick: () => router.visit(route('patients.create')),
    },
    {
        label: 'View',
        icon: EyeIcon,
        permission: { module: 'patients', action: 'view' },
        onClick: () => router.visit(route('patients.show', patient.id))
    },
    {
        label: 'Edit',
        icon: PencilIcon,
        permission: { module: 'patients', action: 'update' },
        onClick: () => router.visit(route('patients.edit', patient.id))
    },
    {
        label: 'Export Records',
        icon: DownloadIcon,
        permission: { module: 'patients', action: 'export' }, // add if exists
        onClick: () => exportData()
    },
    {
        label: 'View Logs',
        icon: HistoryIcon,
        permission: { module: 'admin', action: 'audit_logs' },
        onClick: () => viewLogs()
    }
]
