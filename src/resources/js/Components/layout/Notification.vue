<script setup>
import { ref } from 'vue'; // Added this
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { BellIcon } from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";
import { onClickOutside } from '@vueuse/core'; // Added this
import { ChevronRightIcon } from 'lucide-vue-next';
import { useNotificationStore } from "@/core/stores/useNotificationStore.js";
import {registerForPush} from "@/core/services/push.js";

const notify = useNotificationStore();

// --- ADD THIS LOGIC ---
const isOpen = ref(false);
const dropdownContainer = ref(null);

const toggleNotifications = () => {
    isOpen.value = !isOpen.value;
};

onClickOutside(dropdownContainer, () => {
    isOpen.value = false;
});

const userImg = {
    imageUrl: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e',
}

const props = defineProps({
    name: String,
    role: String
})

const permissionGranted = ref(Notification.permission === 'granted');

const requestPermission = async () => {
    try {
        const token = await registerForPush();

        if (token) {
            permissionGranted.value = true;
            alert("Notifications enabled successfully!");
        } else {
            alert("Notifications were not enabled. Please check your browser settings.");
        }
    } catch (error) {
        console.error("Error during push registration:", error);
    }
};

const userNavigation = [
    { name: 'Profile', href: '/profile' },
    { name: 'Settings', href: '#' },
    { name: 'Sign out', href: '/logout' },
]
</script>

<template>
    <div class="flex items-center gap-2 sm:gap-3">

        <!-- Notification Bell -->
        <div ref="dropdownContainer" class="relative">
            <button
                @click="toggleNotifications"
                class="rounded-full p-1.5 hover:bg-white/10 transition-colors relative"
            >
                <BellIcon class="size-6 text-white" />
                <span
                    v-if="notify.unreadCount > 0"
                    class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[9px] font-bold rounded-full h-4 w-4 flex items-center justify-center border-2 border-secondary"
                >
                    {{ notify.unreadCount }}
                </span>
            </button>

            <!-- Responsive Dropdown: Use max-w-[calc(100vw-2rem)] for mobile -->
            <div v-if="isOpen"
                 class="absolute right-0 mt-3 w-80 sm:w-96 max-w-[calc(100vw-2rem)] bg-white border border-slate-200 rounded-xl shadow-2xl z-50 overflow-hidden animate-in fade-in zoom-in duration-200"
            >
                <!-- ... (Internal notification content remains the same) ... -->
            </div>
        </div>

        <!-- User Profile Menu -->
        <Menu as="div" class="relative flex items-center gap-2">
            <!-- Name & Role: Hidden on very small screens, shown on small up -->
            <div class="hidden sm:flex flex-col items-end leading-none">
                <span class="text-xs font-semibold text-white">{{ name }}</span>
                <span class="text-[10px] text-white/70 uppercase">{{ role }}</span>
            </div>

            <MenuButton class="flex rounded-full transition-transform active:scale-95 flex-shrink-0">
                <img
                    class="size-8 rounded-full border-2 border-white/20 object-cover"
                    :src="userImg.imageUrl"
                    alt="User Profile"
                />
            </MenuButton>

            <!-- Transition and MenuItems ... -->
            <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <MenuItems
                    class="absolute right-0 top-full mt-2 w-48 origin-top-right rounded-xl bg-white p-1 text-gray-700 shadow-xl ring-1 ring-black/5 focus:outline-none"
                >
                    <!-- Show Name/Role inside the menu ONLY on mobile since it's hidden in the header -->
                    <div class="sm:hidden px-4 py-2 border-b border-slate-100 mb-1">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ name }}</p>
                        <p class="text-[10px] text-slate-500 uppercase">{{ role }}</p>
                    </div>

                    <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                        <Link :href="item.href" :class="[active ? 'bg-primary/10 text-primary' : '', 'block px-4 py-2 text-sm rounded-lg font-medium transition-colors']">
                            {{ item.name }}
                        </Link>
                    </MenuItem>
                    <!-- ... rest of your menu items ... -->
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>
