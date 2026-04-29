import {router} from "@inertiajs/vue3";
import {DownloadIcon, EyeIcon, FolderOpen, MailOpen, MergeIcon, PencilIcon, PlusIcon} from "lucide-vue-next";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";

export const RegistryActions = (uuid = null) => {
        const notify = useNotificationStore()

    return [

    // {
    //     label: 'Open File',
    //     icon: MailOpen,
    //     // Using optional chaining and a conditional check
    //     hidden: !uuid,
    //     permission: { module: 'registry', action: 'view' },
    //     onClick: () => uuid
    //         ? router.visit(route('users.show', uuid))
    //         : notify.info("Please select exactly one file")
    // },
    //     {
    //         label: 'Merge File',
    //         icon: FolderOpen,
    //         // Using optional chaining and a conditional check
    //         hidden: !uuid,
    //         permission: { module: 'registry', action: 'view' },
    //         onClick: () => uuid
    //             ? router.visit(route('users.show', uuid))
    //             : notify.info("Please select exactly one file")
    //     },
    {
        label: 'Edit',
        icon: PencilIcon,
        hidden: !uuid,
        permission: { module: 'temporary_files', action: 'registry:UpdateTempFile' },
        onClick: () => uuid
            ? router.visit(route('register.edit', uuid))
            : notify.info("Please select exactly one user")
    },

]};
