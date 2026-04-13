import {router} from "@inertiajs/vue3";
import {DownloadIcon, EyeIcon, PencilIcon, PlusIcon} from "lucide-vue-next";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";

export const UserActions = (user = null) => {
        const notify = useNotificationStore()

    return [

    {
        label: 'New User',
        icon: PlusIcon,
        permission: { module: 'users', action: 'create' },
        onClick: () => router.visit(route('users.create')),
    },
    {
        label: 'View',
        icon: EyeIcon,
        // Using optional chaining and a conditional check
        hidden: !user,
        permission: { module: 'users', action: 'view' },
        onClick: () => user
            ? router.visit(route('users.show', user.id))
            : notify.info("Please select exactly one user")
    },
    {
        label: 'Edit',
        icon: PencilIcon,
        hidden: !user,
        permission: { module: 'users', action: 'update' },
        onClick: () => user
            ? router.visit(route('users.edit', user.id))
            : notify.info("Please select exactly one user")
    },
    {
        label: 'Import User Data',
        icon: DownloadIcon,
        permission: { module: 'users', action: 'create' }, // add if exists
        onClick: () => router.visit(route('users.import-user-data')),
    },
]};
