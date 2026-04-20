<script setup>
import { ref } from 'vue'; // Added this
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { BellIcon } from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";
import { onClickOutside } from '@vueuse/core'; // Added this
import { ChevronRightIcon } from 'lucide-vue-next';
import { useNotificationStore } from "@/core/stores/useNotificationStore.js";

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

const userNavigation = [
    { name: 'Profile', href: '/profile' },
    { name: 'Settings', href: '#' },
    { name: 'Sign out', href: '/logout' },
]
</script>

<template>
    <div class="flex items-center gap-3">

        <div ref="dropdownContainer" class="relative">
            <button
                @click="toggleNotifications"
                class="rounded-full p-1.5 hover:bg-secondary transition-colors relative"
            >
                <BellIcon class="size-6" />
                <span
                    v-if="notify.unreadCount > 0"
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white"
                >
                    {{ notify.unreadCount }}
                </span>
            </button>

            <div v-if="isOpen"
                 class="absolute right-0 mt-3 w-96 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 overflow-hidden animate-in fade-in zoom-in duration-200"
            >
                <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-slate-900">Notifications</h3>
                        <p class="text-[11px] text-slate-500 font-medium">You have {{ notify.unreadCount }} unread alerts</p>
                    </div>
                    <button
                        @click="notify.markAllAsRead"
                        class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 px-2 py-1 rounded-md transition-colors"
                    >
                        Mark all read
                    </button>
                </div>

                <div class="max-h-[450px] overflow-y-auto bg-slate-50/30">
                    <div v-if="notify.notifications.length === 0" class="flex flex-col items-center justify-center py-12 px-4">
                        <div class="bg-slate-100 rounded-full p-3 mb-3">
                            <BellIcon class="size-6 text-slate-400" />
                        </div>
                        <p class="text-slate-500 text-sm font-medium">No notifications yet</p>
                    </div>

                    <div v-else>
                        <div
                            v-for="item in notify.notifications"
                            :key="item.id"
                            @click="notify.markAsRead(item.id)"
                            class="group relative p-4 border-b border-slate-100 last:border-0 hover:bg-white transition-all cursor-pointer"
                            :class="{ 'bg-blue-50/40': !item.read_at }"
                        >
                            <span v-if="!item.read_at" class="absolute left-1.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-blue-600 rounded-full"></span>

                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between items-start gap-2">
                        <span
                            class="text-sm leading-tight text-slate-900 transition-colors"
                            :class="!item.read_at ? 'font-bold' : 'font-medium text-slate-600'"
                        >
                            {{ item.data?.subject || 'System Alert' }}
                        </span>
                                    <span class="text-[10px] text-slate-400 whitespace-nowrap">
                            {{ new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                        </span>
                                </div>

                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ item.data?.message }}
                                </p>

                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center gap-2">
                            <span
                                class="text-[9px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded shadow-sm border"
                                :class="{
                                    'bg-red-50 text-red-700 border-red-100': item.data?.priority === 'high',
                                    'bg-amber-50 text-amber-700 border-amber-100': item.data?.priority === 'medium',
                                    'bg-slate-100 text-slate-600 border-slate-200': item.data?.priority !== 'high' && item.data?.priority !== 'medium'
                                }"
                            >
                                {{ item.data?.priority || 'Normal' }}
                            </span>
                                        <span class="text-[10px] text-slate-400 italic">By {{ item.data?.sender || 'System' }}</span>
                                    </div>

                                    <ChevronRightIcon class="size-3 text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-2 border-t border-slate-100 text-center bg-white">
                    <Link
                        href="/notifications"
                        class="block w-full py-2 text-xs font-bold text-slate-600 hover:text-primary-600 hover:bg-slate-50 rounded-lg transition-colors"
                    >
                        View All History
                    </Link>
                </div>
            </div>
        </div>

        <Menu as="div" class="relative flex items-center gap-3">
            <div class="hidden lg:flex flex-col items-end leading-none">
                <span class="text-xs font-semibold">{{ name }}</span>
                <span class="text-[10px] text-white/70 uppercase">{{role}}</span>
            </div>

            <MenuButton class="flex rounded-full transition-transform active:scale-95">
                <img
                    class="size-8 rounded-full border-2 border-white/20 object-cover"
                    :src="userImg.imageUrl"
                    alt="User Profile"
                />
            </MenuButton>

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
                    <MenuItem
                        v-for="item in userNavigation"
                        :key="item.name"
                        v-slot="{ active }"
                    >
                        <Link
                            :href="item.href"
                            :class="[
                      active ? 'bg-primary/10 text-primary' : '',
                      'block px-4 py-2 text-sm rounded-lg font-medium transition-colors'
                    ]"
                        >
                            {{ item.name }}
                        </Link>
                    </MenuItem>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

